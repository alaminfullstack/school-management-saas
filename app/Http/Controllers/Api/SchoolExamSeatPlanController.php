<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExamSeatPlan;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolExamSeatPlanController extends Controller
{
    /**
     * Get the school record for the authenticated user.
     */
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool();
        $school_id = $school ? $school->id : null;

        $query = DB::table('school_exam_seat_plans as seat')
            ->join('admission_students as student', 'seat.student_id_number', '=', 'student.student_id_number')
            ->where('seat.school_id', $school_id)
            ->select('seat.*', 'student.student_name');

        // Apply Filters
        if ($request->filled('class_name')) $query->where('seat.class_name', $request->class_name);
        if ($request->filled('group_name')) $query->where('seat.group_name', $request->group_name);
        if ($request->filled('section_name')) $query->where('seat.section_name', $request->section_name);
        if ($request->filled('session_name')) $query->where('seat.session_name', $request->session_name);
        if ($request->filled('exam_name')) $query->where('seat.exam_name', $request->exam_name);

        if ($request->filled('search')) {
            $searchTerm = "%{$request->search}%";
            $query->where(function ($q) use ($searchTerm) {
                $q->where('seat.student_id_number', 'like', $searchTerm)
                    ->orWhere('student.student_name', 'like', $searchTerm);
            });
        }

        $query->latest('seat.created_at');

        // If 'all' is present, we return everything for printing along with school details
        if ($request->has('all') && $request->all == 'true') {
            $data = $query->get();

            // Format school logo and location
            $schoolData = null;
            if ($school) {
                $schoolData = [
                    'school_name' => $school->school_name,
                    'logo' => $school->logo ? asset('storage/' . $school->logo) : null,
                    'location' => trim($school->upazila . ', ' . $school->division, ', '),
                    'upazila' => $school->upazila,
                    'division' => $school->division
                ];
            }

            return response()->json([
                'data' => $data,
                'school' => $schoolData
            ]);
        }

        return response()->json($query->paginate(10));
    }

    public function store(Request $request)
    {
        $school = $this->getSchool();
        $school_id = $school ? $school->id : null;
        $students = $request->students;
        $startNum = (int)$request->seat_number_start;

        DB::beginTransaction();
        try {
            foreach ($students as $index => $student) {
                $studentId = $student['student_id_number'];
                $assignedSeat = $startNum + $index;

                // Check if entry already exists for this specific student + exam
                $exists = SchoolExamSeatPlan::where([
                    'school_id' => $school_id,
                    'exam_name' => $request->exam_name,
                    'student_id_number' => $studentId,
                ])->exists();

                if ($exists) {
                    return response()->json([
                        'status' => 'exists',
                        'message' => "Seat Plan already exists for Student ID: {$studentId} in this exam. Please delete previous record to regenerate."
                    ], 422);
                }

                SchoolExamSeatPlan::create([
                    'school_id' => $school_id,
                    'class_name' => $request->class_name,
                    'group_name' => $request->group_name,
                    'section_name' => $request->section_name,
                    'session_name' => $request->session_name,
                    'exam_name' => $request->exam_name,
                    'student_id_number' => $studentId,
                    'seat_number' => $assignedSeat,
                    'seat_number_start' => $request->seat_number_start,
                    'seat_number_end' => $request->seat_number_end,
                ]);
            }
            DB::commit();
            return response()->json(['message' => 'Seat plans generated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool();
        $school_id = $school ? $school->id : null;
        $seat = SchoolExamSeatPlan::where('school_id', $school_id)->findOrFail($id);

        $seat->update([
            'seat_number' => $request->seat_number,
        ]);

        return response()->json(['message' => 'Seat number updated successfully']);
    }

    public function destroy($id)
    {
        $school = $this->getSchool();
        $school_id = $school ? $school->id : null;
        SchoolExamSeatPlan::where('school_id', $school_id)->where('id', $id)->delete();
        return response()->json(['message' => 'Record deleted successfully']);
    }
}
