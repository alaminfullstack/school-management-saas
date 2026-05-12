<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolDueListController extends Controller
{
    public function index(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();
        $schoolId = $school ? $school->id : Auth::user()->school_id;

        if (!$schoolId) {
            return response()->json(['data' => [], 'message' => 'Unauthorized'], 403);
        }

        $search = $request->query('search');
        $all = $request->query('all'); // Check if all data is requested

        // Use a subquery for last_pay_date to prevent row duplication
        $query = SchoolPayment::with([
            'student.schoolClass',
            'student.schoolGroup',
            'student.schoolSection',
            'student.schoolSession'
        ])
            ->select('school_payments.*')
            ->selectSub(function ($q) use ($schoolId) {
                $q->from('school_fee_types')
                    ->select('pay_date')
                    ->whereColumn('fee_type_name', 'school_payments.fees_type')
                    ->where('school_id', $schoolId)
                    ->limit(1);
            }, 'last_pay_date')
            ->where('school_payments.school_id', $schoolId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sub) use ($search) {
                    $sub->where('student_name', 'like', "%{$search}%")
                        ->orWhere('student_id_number', 'like', "%{$search}%");
                })->orWhere('fees_type', 'like', "%{$search}%");
            });
        }

        $today = Carbon::today();

        // If 'all' parameter is present, return all data without pagination
        if ($all) {
            $data = $query->orderBy('pay_date', 'desc')->get();
            
            $data->transform(function ($item) use ($today) {
                $rawLastDate = $item->last_pay_date ? Carbon::parse($item->last_pay_date) : null;

                // Initialize logic variables
                $item->overdue_penalty = 0;
                $item->has_alert_penalty = false;

                // Logic: If date passed and there is a due balance
                if ($rawLastDate && $today->gt($rawLastDate) && (float)$item->total_due > 0) {
                    // Transfer the due amount to overdue column
                    $item->overdue_penalty = (float)$item->total_due;
                    $item->has_alert_penalty = true;
                    
                    // Set total_due to 0 for the frontend display
                    $item->total_due = 0;
                }

                // Status Logic
                $currentDue = (float)$item->total_due;
                $currentOverdue = (float)$item->overdue_penalty;
                $paidAmount = (float)$item->total_amount;

                if ($currentDue <= 0 && $currentOverdue <= 0) {
                    $item->status = 'paid';
                } elseif ($paidAmount > 0 && ($currentDue > 0 || $currentOverdue > 0)) {
                    $item->status = 'partial';
                } else {
                    $item->status = 'unpaid';
                }

                // Final payable is the sum of whatever is left in due OR the overdue amount
                $item->final_payable_total = $currentDue + $currentOverdue;

                // FORMAT DATES
                $item->display_last_pay_date = $rawLastDate ? $rawLastDate->format('d/m/Y') : 'N/A';
                $item->display_pay_date = $item->pay_date ? Carbon::parse($item->pay_date)->format('d/m/Y') : 'N/A';

                // Map keys for JS
                $item->payment_id = $item->id;
                $item->class   = $item->student->schoolClass->class_name ?? 'N/A';
                $item->group   = $item->student->schoolGroup->group_name ?? 'N/A';
                $item->section = $item->student->schoolSection->section_name ?? 'N/A';
                $item->session = $item->student->schoolSession->session_year ?? 'N/A';
                $item->student_name = $item->student->student_name ?? 'Unknown';
                $item->student_id_number = $item->student->student_id_number ?? '---';
                $item->payment_status = $item->status;
                $item->fee_name = $item->fee_name;

                return $item;
            });

            return response()->json($data);
        }

        // Otherwise, return paginated data
        $paginatedData = $query->orderBy('pay_date', 'desc')->paginate(10);

        $paginatedData->getCollection()->transform(function ($item) use ($today) {
            $rawLastDate = $item->last_pay_date ? Carbon::parse($item->last_pay_date) : null;

            // Initialize logic variables
            $item->overdue_penalty = 0;
            $item->has_alert_penalty = false;

            // Logic: If date passed and there is a due balance
            if ($rawLastDate && $today->gt($rawLastDate) && (float)$item->total_due > 0) {
                // Transfer the due amount to overdue column
                $item->overdue_penalty = (float)$item->total_due;
                $item->has_alert_penalty = true;
                
                // Set total_due to 0 for the frontend display
                $item->total_due = 0;
            }

            // Status Logic
            $currentDue = (float)$item->total_due;
            $currentOverdue = (float)$item->overdue_penalty;
            $paidAmount = (float)$item->total_amount;

            if ($currentDue <= 0 && $currentOverdue <= 0) {
                // Fully cleared
                $item->status = 'paid';
            } elseif ($paidAmount > 0 && ($currentDue > 0 || $currentOverdue > 0)) {
                // Any payment made but balance remains
                $item->status = 'partial';
            } else {
                // No payment made and balance remains
                $item->status = 'unpaid';
            }

            // Final payable is the sum of whatever is left in due OR the overdue amount
            $item->final_payable_total = $currentDue + $currentOverdue;

            // FORMAT DATES
            $item->display_last_pay_date = $rawLastDate ? $rawLastDate->format('d/m/Y') : 'N/A';
            $item->display_pay_date = $item->pay_date ? Carbon::parse($item->pay_date)->format('d/m/Y') : 'N/A';

            // Map keys for JS
            $item->payment_id = $item->id;
            $item->class   = $item->student->schoolClass->class_name ?? 'N/A';
            $item->group   = $item->student->schoolGroup->group_name ?? 'N/A';
            $item->section = $item->student->schoolSection->section_name ?? 'N/A';
            $item->session = $item->student->schoolSession->session_year ?? 'N/A';
            $item->student_name = $item->student->student_name ?? 'Unknown';
            $item->student_id_number = $item->student->student_id_number ?? '---';
            $item->payment_status = $item->status;
            $item->fee_name = $item->fee_name;

            return $item;
        });

        return response()->json($paginatedData);
    }

    public function updatePayment(Request $request, $id)
    {
        $school = School::where('user_id', Auth::id())->first();
        $schoolId = $school ? $school->id : Auth::user()->school_id;

        $payment = SchoolPayment::where('id', $id)
            ->where('school_id', $schoolId)
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $paying = (float)$request->paying_amount;
        $totalOutstanding = (float)$payment->total_due;

        // Subtract payment from current due
        $newDue = max(0, $totalOutstanding - $paying);
        $newPaidTotal = (float)$payment->total_amount + $paying;

        // Status Logic for DB storage
        if ($newDue <= 0) {
            $status = 'paid';
        } elseif ($newPaidTotal > 0 && $newDue > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

        $payment->update([
            'total_amount' => $newPaidTotal,
            'total_due' => $newDue,
            'payable_due' => $newDue,
            'status' => $status,
            'pay_method' => $request->pay_method,
            'pay_date' => $request->pay_date ?? now()->format('Y-m-d')
        ]);

        return response()->json(['message' => 'Payment Updated Successfully']);
    }

    public function destroy($id)
    {
        $school = School::where('user_id', Auth::id())->first();
        $schoolId = $school ? $school->id : Auth::user()->school_id;

        $payment = SchoolPayment::where('id', $id)
            ->where('school_id', $schoolId)
            ->first();

        if (!$payment) {
            return response()->json(['message' => 'Record not found or unauthorized'], 404);
        }

        try {
            $payment->delete();
            return response()->json(['message' => 'Record deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error: Could not delete record'], 500);
        }
    }
}