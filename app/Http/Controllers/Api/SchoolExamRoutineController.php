<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExamRoutine;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SchoolExamRoutineController extends Controller
{
    /**
     * Helper to get the school instance for the logged-in user.
     */
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    /**
     * Display a listing of the routines with advanced filtering and pagination.
     */
    public function index(Request $request)
    {
        $school = $this->getSchool();

        if (!$school) {
            return response()->json(['data' => [], 'message' => 'School context not found'], 404);
        }

        $query = SchoolExamRoutine::where('school_id', $school->id);

        // Advanced Filtering Logic
        $filters = ['class_name', 'group_name', 'section_name', 'session_name', 'exam_name'];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        // Fetching records ordered by date and time
        $perPage = $request->input('per_page', 15);
        $routines = $query->orderBy('exam_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate($perPage);

        return response()->json($routines);
    }

    /**
     * Store a newly created routine in storage.
     */
    public function store(Request $request)
    {
        $school = $this->getSchool();

        if (!$school) {
            return response()->json(['message' => 'Unauthorized school context.'], 403);
        }

        // Added explicit validation for premium standard data integrity
        $validator = Validator::make($request->all(), [
            'class_name'   => 'required|string',
            'subject_name' => 'required|string',
            'exam_name'    => 'required|string',
            'exam_date'    => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Strict Unique Check: Prevent duplicate subjects within the same exam scope
        $exists = SchoolExamRoutine::where([
            'school_id'    => $school->id,
            'class_name'   => $request->class_name,
            'group_name'   => $request->group_name,
            'section_name' => $request->section_name,
            'session_name' => $request->session_name,
            'exam_name'    => $request->exam_name,
            'subject_name' => $request->subject_name,
        ])->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Routine already exists for this Class, Section, and Subject in this Exam.'
            ], 422);
        }

        $data = $request->all();
        $data['school_id'] = $school->id;

        $routine = SchoolExamRoutine::create($data);

        return response()->json([
            'message' => 'Exam Routine created successfully',
            'data'    => $routine
        ], 201);
    }

    /**
     * Display the specified routine.
     */
    public function show($id)
    {
        $school = $this->getSchool();
        $routine = SchoolExamRoutine::where('school_id', $school->id)->findOrFail($id);

        return response()->json($routine);
    }

    /**
     * Update the specified routine in storage.
     */
    public function update(Request $request, $id)
    {
        $school = $this->getSchool();
        $routine = SchoolExamRoutine::where('school_id', $school->id)->findOrFail($id);

        // Conflict check excluding current record
        $exists = SchoolExamRoutine::where([
            'school_id'    => $school->id,
            'class_name'   => $request->class_name ?? $routine->class_name,
            'group_name'   => $request->group_name ?? $routine->group_name,
            'section_name' => $request->section_name ?? $routine->section_name,
            'session_name' => $request->session_name ?? $routine->session_name,
            'exam_name'    => $request->exam_name ?? $routine->exam_name,
            'subject_name' => $request->subject_name ?? $routine->subject_name,
        ])->where('id', '!=', $id)->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Another routine already exists with this configuration.'
            ], 422);
        }

        $routine->update($request->all());

        return response()->json([
            'message' => 'Exam Routine updated successfully',
            'data'    => $routine
        ]);
    }

    /**
     * Remove the specified routine from storage.
     */
    public function destroy($id)
    {
        $school = $this->getSchool();
        $routine = SchoolExamRoutine::where('school_id', $school->id)->findOrFail($id);

        $routine->delete();

        return response()->json(['message' => 'Exam Routine deleted successfully']);
    }
}
