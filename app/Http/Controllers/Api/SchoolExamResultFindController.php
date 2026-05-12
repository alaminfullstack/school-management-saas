<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use Illuminate\Support\Facades\Auth;

class SchoolExamResultFindController extends Controller
{
    public function findResult(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json(['message' => 'School context not found'], 404);
        }

        // --- MODE: SINGLE RESULT (Transcript) ---
        if ($request->mode === 'single') {
            $request->validate([
                'student_id' => 'required',
                'admit_no' => 'required',
            ]);

            $admitCard = DB::table('school_exam_admit_cards as ac')
                ->join('admission_students as s', 'ac.student_id_number', '=', 's.student_id_number')
                ->where('ac.school_id', $school->id)
                ->where('ac.student_id_number', $request->student_id)
                ->where('ac.admit_card_number', $request->admit_no)
                ->select(
                    's.student_name',
                    's.father_name',
                    'ac.student_id_number',
                    'ac.class_name',
                    'ac.group_name',
                    'ac.section_name',
                    'ac.exam_name',
                    'ac.session_name',
                    'ac.admit_card_number'
                )
                ->first();

            if (!$admitCard) {
                return response()->json(['message' => 'Invalid Student ID or Admit Card Number'], 422);
            }

            $marks = DB::table('school_exam_marks')
                ->where('school_id', $school->id)
                ->where('student_id_number', $request->student_id)
                ->where('exam_name', $admitCard->exam_name)
                ->get();

            if ($marks->isEmpty()) {
                return response()->json(['message' => 'No marks found for this exam record'], 404);
            }

            // FIX: Added unique('letter_name') to prevent duplicates from subject-wise entries
            $gradingScale = DB::table('school_exam_grades')
                ->where('school_id', $school->id)
                ->where('class_name', $admitCard->class_name)
                ->select('min_mark', 'max_mark', 'letter_name', 'number_point')
                ->orderBy('number_point', 'desc')
                ->get()
                ->unique('letter_name')
                ->values();

            return response()->json([
                'student_name'      => $admitCard->student_name,
                'father_name'       => $admitCard->father_name,
                'student_id_number' => $admitCard->student_id_number,
                'class_name'        => $admitCard->class_name,
                'group_name'        => $admitCard->group_name,
                'section_name'      => $admitCard->section_name,
                'admit_card_number' => $admitCard->admit_card_number,
                'roll_no'           => $marks->first()->roll_no ?? 'N/A',
                'exam_name'         => $admitCard->exam_name,
                'session_name'      => $admitCard->session_name,
                'school_info' => [
                    'school_name' => $school->school_name,
                    'village'     => $school->village,
                    'upazila'     => $school->upazila,
                    'district'    => $school->district,
                    'division'    => $school->division,
                    'logo'        => $school->logo ? asset('storage/' . $school->logo) : null,
                ],
                'gpa'               => number_format($marks->avg('point'), 2),
                'subjects'          => $marks->map(function ($m) {
                    return [
                        'name'  => $m->subject_name,
                        'mark'  => $m->mark,
                        'grade' => $m->letter_name,
                        'point' => $m->point
                    ];
                }),
                'grading_scale'     => $gradingScale
            ]);
        }

        // --- MODE: CLASSWISE RESULT ---
        else {
            $request->validate([
                'class'   => 'required',
                'exam'    => 'required',
                'session' => 'required',
            ]);

            $allMarks = DB::table('school_exam_marks')
                ->where('school_id', $school->id)
                ->where('class_name', $request->class)
                ->where('exam_name', $request->exam)
                ->where('session_name', $request->session)
                ->when($request->section, function ($q) use ($request) {
                    return $q->where('section_name', $request->section);
                })
                ->when($request->group, function ($q) use ($request) {
                    return $q->where('group_name', $request->group);
                })
                ->get();

            if ($allMarks->isEmpty()) {
                return response()->json(['message' => 'No results found for this selection'], 404);
            }

            $subjectsList = $allMarks->pluck('subject_name')->unique()->values();

            // FIX: Added unique('letter_name') to prevent duplicates for classwise results
            $gradingScale = DB::table('school_exam_grades')
                ->where('school_id', $school->id)
                ->where('class_name', $request->class)
                ->select('min_mark', 'max_mark', 'letter_name', 'number_point')
                ->orderBy('number_point', 'desc')
                ->get()
                ->unique('letter_name')
                ->values();

            $studentsData = $allMarks->groupBy('student_id_number')->map(function ($marks, $studentId) use ($gradingScale) {
                $first = $marks->first();
                $subjectMarks = [];
                foreach ($marks as $m) {
                    $subjectMarks[$m->subject_name] = $m->mark;
                }
                $avgPoint = $marks->avg('point');

                return [
                    'id'         => $first->id,
                    'student_id' => $studentId,
                    'name'       => $first->student_name ?? 'N/A',
                    'marks'      => $subjectMarks,
                    'total'      => $marks->sum('mark'),
                    'gpa'        => number_format($avgPoint, 2),
                    'grade'      => $this->calculateFinalGrade($avgPoint, $gradingScale)
                ];
            })->values();

            return response()->json([
                'school_name'   => $school->school_name,
                'location'      => $school->upazila . ', ' . $school->district,
                'school_logo'   => $school->logo ? asset('storage/' . $school->logo) : null,
                'subjects_list' => $subjectsList,
                'students'      => $studentsData,
                'grading_scale' => $gradingScale
            ]);
        }
    }

    private function calculateFinalGrade($gpa, $grades = null)
    {
        if (!$grades || count($grades) == 0) {
            if ($gpa >= 5.0) return 'A+';
            if ($gpa >= 4.0) return 'A';
            if ($gpa >= 3.5) return 'A-';
            if ($gpa >= 3.0) return 'B';
            if ($gpa >= 2.0) return 'C';
            if ($gpa >= 1.0) return 'D';
            return 'F';
        }

        foreach ($grades as $grade) {
            if ($gpa >= $grade->number_point) {
                return $grade->letter_name;
            }
        }
        return 'F';
    }

    public function destroy($id)
    {
        DB::table('school_exam_marks')->where('id', $id)->delete();
        return response()->json(['message' => 'Record deleted successfully']);
    } 
}