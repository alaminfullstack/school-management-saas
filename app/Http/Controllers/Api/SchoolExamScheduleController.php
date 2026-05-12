<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExamSchedule;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolExamScheduleController extends Controller
{
    public function index(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();
        $query = SchoolExamSchedule::where('school_id', $school->id);

        if ($request->class_name) $query->where('class_name', $request->class_name);
        if ($request->group_name) $query->where('group_name', $request->group_name);
        if ($request->section_name) $query->where('section_name', $request->section_name);
        if ($request->session_name) $query->where('session_name', $request->session_name);
        if ($request->exam_name) $query->where('exam_name', $request->exam_name);

        return response()->json($query->orderBy('id', 'desc')->paginate(50));
    }

    public function getCounts(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        // 1. Get Total Subjects from school_subjects
        $totalSubjects = DB::table('school_subjects')
            ->where('school_id', $school->id)
            ->where('class_id', $request->class_id) // Using IDs for accuracy if available
            ->count();

        // 2. Get Submitted Subjects from school_exam_marks (distinct subject_name)
        $submittedSubjects = DB::table('school_exam_marks')
            ->where('school_id', $school->id)
            ->where('class_name', $request->class_name)
            ->where('exam_name', $request->exam_name)
            ->where('session_name', $request->session_name)
            ->distinct('subject_name')
            ->count('subject_name');

        return response()->json([
            'total' => $totalSubjects,
            'submitted' => $submittedSubjects,
            'remaining' => max(0, $totalSubjects - $submittedSubjects)
        ]);
    }

    public function store(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        $exists = SchoolExamSchedule::where([
            'school_id' => $school->id,
            'class_name' => $request->class_name,
            'group_name' => $request->group_name,
            'section_name' => $request->section_name,
            'session_name' => $request->session_name,
            'exam_name' => $request->exam_name,
        ])->first();

        if ($exists) {
            return response()->json([
                'status' => 'exists',
                'message' => "Schedule already exists for this criteria."
            ], 422);
        }

        SchoolExamSchedule::create(array_merge($request->all(), ['school_id' => $school->id]));
        return response()->json(['message' => 'Created successfully']);
    }

    public function show($id)
    {
        $school = School::where('user_id', Auth::id())->first();
        return SchoolExamSchedule::where('school_id', $school->id)->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $school = School::where('user_id', Auth::id())->first();
        $schedule = SchoolExamSchedule::where('school_id', $school->id)->findOrFail($id);
        $schedule->update($request->all());
        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        $school = School::where('user_id', Auth::id())->first();
        SchoolExamSchedule::where('school_id', $school->id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
