<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExamAdmitCard;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolExamAdmitCardController extends Controller
{
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    private function getSchoolId()
    {
        $school = $this->getSchool();
        return $school ? $school->id : null;
    }

    public function index(Request $request)
    {
        $school = $this->getSchool();
        $school_id = $school ? $school->id : null;

        $query = DB::table('school_exam_admit_cards as admit')
            ->join('admission_students as student', 'admit.student_id_number', '=', 'student.student_id_number')
            ->where('admit.school_id', $school_id)
            ->select('admit.*', 'student.student_name');

        // Filter Logic
        if ($request->filled('class_name')) $query->where('admit.class_name', $request->class_name);
        if ($request->filled('group_name')) $query->where('admit.group_name', $request->group_name);
        if ($request->filled('section_name')) $query->where('admit.section_name', $request->section_name);
        if ($request->filled('session_name')) $query->where('admit.session_name', $request->session_name);
        if ($request->filled('exam_name')) $query->where('admit.exam_name', $request->exam_name);

        if ($request->filled('search')) {
            $searchTerm = "%{$request->search}%";
            $query->where(function ($q) use ($searchTerm) {
                $q->where('admit.student_id_number', 'like', $searchTerm)
                    ->orWhere('student.student_name', 'like', $searchTerm);
            });
        }

        $paginatedData = $query->latest('admit.created_at')->paginate(10)->toArray();

        // Attach School Info for Frontend
        if ($school) {
            $paginatedData['school_info'] = [
                'school_name' => $school->school_name,
                'village' => $school->village,
                'upazila' => $school->upazila,
                'division' => $school->division,
                'logo' => $school->logo ? asset('storage/' . $school->logo) : null,
            ];
        }

        return response()->json($paginatedData);
    }

    public function store(Request $request)
    {
        $school_id = $this->getSchoolId();
        $students = $request->students;
        $exam = $request->exam_name;

        // --- Sequential Number Logic ---
        $startNumber = 24951080;
        $lastRecord = SchoolExamAdmitCard::where('school_id', $school_id)
            ->orderBy('id', 'desc')
            ->first();

        $currentAdmitNumber = $lastRecord ? (int)$lastRecord->admit_card_number + 1 : $startNumber;

        DB::beginTransaction();
        try {
            foreach ($students as $student) {
                $studentId = $student['student_id_number'];

                $exists = SchoolExamAdmitCard::where([
                    'school_id' => $school_id,
                    'class_name' => $request->class_name,
                    'exam_name' => $exam,
                    'student_id_number' => $studentId,
                ])->exists();

                if ($exists) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'exists',
                        'message' => "Admit card already exists for Student ID: {$studentId} for this exam."
                    ], 422);
                }

                SchoolExamAdmitCard::create([
                    'school_id' => $school_id,
                    'class_name' => $request->class_name,
                    'group_name' => $request->group_name,
                    'section_name' => $request->section_name,
                    'session_name' => $request->session_name,
                    'exam_name' => $exam,
                    'student_id_number' => $studentId,
                    'admit_card_number' => $currentAdmitNumber,
                    'admit_card_start_number' => $request->admit_card_start_number,
                    'admit_card_end_number' => $request->admit_card_end_number,
                ]);

                $currentAdmitNumber++;
            }

            DB::commit();
            return response()->json(['message' => 'Admit cards generated successfully.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $school_id = $this->getSchoolId();
        $admit = SchoolExamAdmitCard::where('school_id', $school_id)->findOrFail($id);

        $admit->update([
            'exam_name' => $request->exam_name,
            'class_name' => $request->class_name,
            'section_name' => $request->section_name,
            'group_name' => $request->group_name,
            'session_name' => $request->session_name,
            // 'admit_card_number' remains fixed to prevent ID jumping on simple edits
        ]);

        return response()->json(['message' => 'Admit card updated successfully']);
    }

    public function destroy($id)
    {
        $school_id = $this->getSchoolId();
        SchoolExamAdmitCard::where('school_id', $school_id)->where('id', $id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function getStudents(Request $request)
    {
        $school_id = $this->getSchoolId();
        $students = DB::table('admission_students')
            ->where('school_id', $school_id)
            ->where('class', $request->class_name)
            ->where('session', $request->session_name)
            ->get(['student_id_number', 'student_name']);

        return response()->json(['data' => $students]);
    }
}
