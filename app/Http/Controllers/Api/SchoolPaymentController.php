<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdmissionStudent;
use App\Models\School;
use App\Models\SchoolPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolPaymentController extends Controller
{
    /**
     * Get the school associated with the authenticated user.
     */
    private function getSchool()
    {
        $school = School::where('user_id', Auth::id())->first();
        if (!$school) {
            abort(403, 'Unauthorized: No school associated with this account.');
        }
        return $school;
    }

    /**
     * Display a listing of payments scoped to the user's school.
     */
    public function index(Request $request)
    {
        $school = $this->getSchool();

        // Use camelCase to match your Model functions
        $query = SchoolPayment::with([
            'student.schoolClass',
            'student.schoolGroup',
            'student.schoolSection',
            'student.schoolSession'
        ])->where('school_id', $school->id);

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('student', function ($sub) use ($s) {
                    $sub->where('student_name', 'like', "%$s%")
                        ->orWhere('student_id_number', 'like', "%$s%");
                })
                    ->orWhere('fees_type', 'like', "%$s%")
                    ->orWhere('pay_method', 'like', "%$s%");
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request)
    {
        $school = $this->getSchool();

        $validated = $request->validate([
            'admission_student_id' => 'required|exists:admission_students,id',
            'fees_type'            => 'required|string',
            'fee_name'             => 'required|string',
            'total_payable'        => 'required|numeric|min:0',
            'type_amount'          => 'required|numeric|min:0',
            'pay_date'             => 'required|date',
            'pay_method'           => 'required|string',
        ]);

        // Check for a student-specific discount to get the real total payable
        $discount = DB::table('school_fee_discounts')
            ->where('school_id', $school->id)
            ->where('student_id', $validated['admission_student_id'])
            ->where('fee_name', $validated['fee_name'])
            ->first();

        $effectiveTotal = $discount
            ? (float) $discount->after_discount
            : (float) $validated['total_payable'];

        // Sum all previous payments for this student + fee combination
        $alreadyPaid = DB::table('school_payments')
            ->where('school_id', $school->id)
            ->where('admission_student_id', $validated['admission_student_id'])
            ->where('fees_type', $validated['fees_type'])
            ->where('fee_name', $validated['fee_name'])
            ->sum('type_amount');

        $paidAmount = (float) $validated['type_amount'];
        $totalPaid  = (float) $alreadyPaid + $paidAmount;
        $dueAmount  = max($effectiveTotal - $totalPaid, 0);

        // Determine status based on cumulative payments
        $status = 'unpaid';
        if ($totalPaid >= $effectiveTotal && $effectiveTotal > 0) {
            $status = 'paid';
        } elseif ($paidAmount > 0 && $dueAmount > 0) {
            $status = 'partial';
        }

        $payment = SchoolPayment::create(array_merge($validated, [
            'school_id'    => $school->id,
            'total_payable'=> $effectiveTotal,
            'payable_due'  => $dueAmount,
            'status'       => $status,
            'total_amount' => $paidAmount,
            'total_due'    => $dueAmount,
        ]));

        return response()->json(['status' => 'success', 'data' => $payment->load('student')], 201);
    }

    public function show($id)
    {
        $school = $this->getSchool();

        // Use camelCase to match your Student model methods
        $payment = SchoolPayment::with([
            'student.schoolClass',
            'student.schoolGroup',
            'student.schoolSection',
            'student.schoolSession'
        ])
            ->where('school_id', $school->id)
            ->findOrFail($id);

        return response()->json($payment);
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, $id)
    {
        $school = $this->getSchool();
        $payment = SchoolPayment::where('school_id', $school->id)->findOrFail($id);

        $validated = $request->validate([
            'fees_type'     => 'sometimes|required|string',
            'fee_name'      => 'sometimes|nullable|string',
            'total_payable' => 'sometimes|required|numeric|min:0',
            'type_amount'   => 'sometimes|required|numeric|min:0',
            'pay_date'      => 'sometimes|required|date',
            'pay_method'    => 'sometimes|required|string',
        ]);

        // Re-calculate dues if amounts are updated
        if (isset($validated['total_payable']) || isset($validated['type_amount'])) {
            $feesType  = $validated['fees_type']  ?? $payment->fees_type;
            $feeName   = $validated['fee_name']   ?? $payment->fee_name;
            $studentId = $payment->admission_student_id;

            // Check for discount
            $discount = DB::table('school_fee_discounts')
                ->where('school_id', $school->id)
                ->where('student_id', $studentId)
                ->where('fee_name', $feeName)
                ->first();

            $effectiveTotal = $discount
                ? (float) $discount->after_discount
                : (float) ($validated['total_payable'] ?? $payment->total_payable);

            // Sum all OTHER payments for this student + fee (excluding current record)
            $alreadyPaid = DB::table('school_payments')
                ->where('school_id', $school->id)
                ->where('admission_student_id', $studentId)
                ->where('fees_type', $feesType)
                ->where('fee_name', $feeName)
                ->where('id', '!=', $id)
                ->sum('type_amount');

            $paidAmount = (float) ($validated['type_amount'] ?? $payment->type_amount);
            $totalPaid  = (float) $alreadyPaid + $paidAmount;
            $due        = max($effectiveTotal - $totalPaid, 0);

            $validated['total_payable'] = $effectiveTotal;
            $validated['payable_due']   = $due;
            $validated['total_due']     = $due;
            $validated['total_amount']  = $paidAmount;

            // Recalculate status
            if ($totalPaid >= $effectiveTotal && $effectiveTotal > 0) {
                $validated['status'] = 'paid';
            } elseif ($paidAmount > 0 && $due > 0) {
                $validated['status'] = 'partial';
            } else {
                $validated['status'] = 'unpaid';
            }
        }

        $payment->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Payment updated successfully',
            'data'    => $payment->load('student')
        ]);
    }

    /**
     * Remove the specified payment.
     */
    public function destroy($id)
    {
        $school = $this->getSchool();
        $payment = SchoolPayment::where('school_id', $school->id)->findOrFail($id);

        $payment->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Record deleted successfully'
        ]);
    }

    /**
     * Calculate the total payable amount for a student, 
     * applying discounts if they exist.
     */
    public function getTotalFee(Request $request)
    {
        $request->validate([
            'fees_type'    => 'required|string',
            'fee_name'     => 'required|string',
            'admission_id' => 'required|integer',
        ]);

        $school = $this->getSchool();

        // 1. Check for student-specific discount first
        $discount = DB::table('school_fee_discounts')
            ->where('school_id', $school->id)
            ->where('student_id', $request->admission_id)
            ->where('fee_name', $request->fee_name)
            ->first();

        if ($discount) {
            // Calculate how much the student has already paid toward this discounted fee
            $alreadyPaid = DB::table('school_payments')
                ->where('school_id', $school->id)
                ->where('admission_student_id', $request->admission_id)
                ->where('fees_type', $request->fees_type)
                ->where('fee_name', $request->fee_name)
                ->sum('type_amount');

            $remainingDue = max((float) $discount->after_discount - (float) $alreadyPaid, 0);

            return response()->json([
                'total_payable' => $discount->after_discount,
                'remaining_due' => $remainingDue,
                'has_discount'  => true
            ]);
        }

        // 2. Fallback to standard fee type amount
        $fee = DB::table('school_fee_types')
            ->where('school_id', $school->id)
            ->where('fee_type_name', $request->fees_type)
            ->where('fee_name', $request->fee_name)
            ->first();

        // Log::info($fee);

        if (!$fee) {
            return response()->json(['message' => 'Fee configuration not found.'], 404);
        }

        // USF written code
        // get the student payment record for the given fee type and fee name
        $paymentRecord = DB::table('school_payments')
            ->where('school_id', $school->id)
            ->where('admission_student_id', $request->admission_id)
            ->where('fees_type', $request->fees_type)
            ->where('fee_name', $request->fee_name)
            ->sum('type_amount'); // Sum of all payments made for this fee type and fee name


        $amountToBePaid = $fee->amount;

        if ($paymentRecord) {
            $amountToBePaid = max($fee->amount - $paymentRecord, 0);
        }

        

        // Log::info($paymentRecord ?? 'No payment record found for this student and fee type.');

        return response()->json([
            'total_payable' => $fee->amount,
            'remaining_due' => $amountToBePaid,
            'has_discount'  => false
        ]);
    }

    /**
     * Download a PDF payment report for a specific student.
     */
    public function downloadPdf(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:admission_students,id',
        ]);

        $school = $this->getSchool();

        $student = AdmissionStudent::with([
            'schoolClass',
            'schoolGroup',
            'schoolSection',
            'schoolSession',
        ])->findOrFail($request->student_id);

        $payments = SchoolPayment::where('school_id', $school->id)
            ->where('admission_student_id', $request->student_id)
            ->orderBy('pay_date', 'asc')
            ->get();

        // Grand Total = unique total_payable per fee (fee amount counted once per fee_name+fees_type combo)
        $grandTotal = $payments
            ->groupBy(fn ($p) => $p->fees_type . '||' . $p->fee_name)
            ->sum(fn ($group) => (float) $group->first()->total_payable);

        // Grand Paid = sum of every individual payment made
        $grandPaid = $payments->sum(fn ($p) => (float) $p->type_amount);

        // Grand Due = what's still owed overall
        $grandDue = max($grandTotal - $grandPaid, 0);

        $pdf = Pdf::loadView('exports.payment_report_pdf', [
            'school'      => $school,
            'student'     => $student,
            'payments'    => $payments,
            'grandTotal'  => $grandTotal,
            'grandPaid'   => $grandPaid,
            'grandDue'    => $grandDue,
            'generatedAt' => Carbon::now()->format('d/m/Y'),
        ])->setPaper('a4', 'landscape');

        $filename = 'payment_report_' . $student->student_id_number . '_' . Carbon::now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}