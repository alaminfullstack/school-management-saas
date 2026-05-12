<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolExpenseController extends Controller
{
    private function getSchool($user)
    {
        return DB::table('schools')->where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json(['data' => []]);
        }

        $query = Expense::where('school_id', $school->id);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('expense_reason', 'like', "%{$request->search}%")
                    ->orWhere('name', 'like', "%{$request->search}%");
                // Mobile search removed
            });
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json(['message' => 'School profile not found.'], 404);
        }

        $request->validate([
            'date' => 'required|date',
            'expense_reason' => 'required|string',
            'name' => 'required|string',
            'amount' => 'required|numeric'
            // Mobile validation removed
        ]);

        $expense = Expense::create([
            'school_id'      => $school->id,
            'date'           => $request->date,
            'expense_reason' => $request->expense_reason,
            'name'           => $request->name,
            'amount'         => $request->amount
            // Mobile storage removed
        ]);

        return response()->json([
            'message' => 'Expense recorded successfully',
            'expense' => $expense
        ]);
    }

    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $expense = Expense::where('school_id', $school->id)->findOrFail($id);

        // Updated to only include relevant fields (mobile excluded)
        $expense->update($request->only(['date', 'expense_reason', 'name', 'amount']));

        return response()->json(['message' => 'Expense updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $expense = Expense::where('school_id', $school->id)->findOrFail($id);
        $expense->delete();

        return response()->json(['message' => 'Expense deleted']);
    }

    public function export(Request $request)
    {
        $school = $this->getSchool($request->user());
        $expenses = Expense::where('school_id', $school->id)->latest()->get();

        $filename = "expense_report_" . date('Y-m-d') . ".csv";

        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            // Removed 'Mobile' from header
            fputcsv($file, ['Date', 'Reason', 'Name', 'Amount']);

            foreach ($expenses as $e) {
                // Removed $e->mobile from data rows
                fputcsv($file, [$e->date, $e->expense_reason, $e->name, $e->amount]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename"
        ]);
    }
}
