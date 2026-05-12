<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSyllabus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SchoolSyllabusController extends Controller
{
    /**
     * Display a listing of the syllabuses with relationships.
     */
    public function index(Request $request)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        $query = SchoolSyllabus::where('school_id', $school->id)->with([
            'school_session',
            'school_class',
            'school_group',
            'school_section',
            'school_subject'
        ]);

        // Filter by class_id
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by group_id
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Filter by section_id
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Filter by session_id
        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        // Filter by exam_name
        if ($request->filled('exam_name')) {
            $query->where('exam_name', $request->exam_name);
        }

        // Search Logic
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('exam_name', 'like', "%{$search}%")
                    ->orWhere('start_page', 'like', "%{$search}%")
                    ->orWhere('end_page', 'like', "%{$search}%")
                    ->orWhereHas('school_subject', function ($sub) use ($search) {
                        $sub->where('subject_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('school_session', function ($sess) use ($search) {
                        $sess->where('session_year', 'like', "%{$search}%");
                    })
                    ->orWhereHas('school_class', function ($cls) use ($search) {
                        $cls->where('class_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('school_section', function ($sec) use ($search) {
                        $sec->where('section_name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Store a newly created syllabus in storage.
     */
    public function store(Request $request)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:school_sessions,id',
            'class_id'   => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:school_subjects,id',
            'exam_name'  => 'required|string|max:255',
            'group_id'   => 'nullable|exists:school_groups,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'start_page' => 'nullable|string|max:255',
            'end_page'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = array_merge($request->all(), ['school_id' => $school->id]);
        $syllabus = SchoolSyllabus::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Syllabus created successfully',
            'data' => $syllabus
        ], 201);
    }

    /**
     * Display the specified syllabus with all relationships.
     */
    public function show($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        $syllabus = SchoolSyllabus::where('school_id', $school->id)->with([
            'school_session',
            'school_class',
            'school_group',
            'school_section',
            'school_subject'
        ])->find($id);

        if (!$syllabus) {
            return response()->json(['message' => 'Syllabus not found'], 404);
        }

        return response()->json($syllabus);
    }

    /**
     * Update the specified syllabus in storage.
     */
    public function update(Request $request, $id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $syllabus = SchoolSyllabus::where('school_id', $school->id)->find($id);

        if (!$syllabus) {
            return response()->json(['message' => 'Syllabus not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:school_sessions,id',
            'class_id'   => 'required|exists:school_classes,id',
            'subject_id' => 'required|exists:school_subjects,id',
            'exam_name'  => 'required|string|max:255',
            'group_id'   => 'nullable|exists:school_groups,id',
            'section_id' => 'nullable|exists:school_sections,id',
            'start_page' => 'nullable|string|max:255',
            'end_page'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $syllabus->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Syllabus updated successfully',
            'data' => $syllabus
        ]);
    }

    /**
     * Remove the specified syllabus from storage.
     */
    public function destroy($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $syllabus = SchoolSyllabus::where('school_id', $school->id)->find($id);

        if (!$syllabus) {
            return response()->json(['message' => 'Syllabus not found'], 404);
        }

        $syllabus->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Syllabus deleted successfully'
        ]);
    }
    
      // School Syllabus filter
    public function syllabusFilter(Request $request)
    {
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.',
                'data' => []
            ], 404);
        }

        $query = SchoolSyllabus::where('school_id', $school->id)
            ->with([
                'school_session',
                'school_class',
                'school_group',
                'school_section',
                'school_subject'
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                // Direct fields
                $q->where('exam_name', 'like', "%{$search}%")
                ->orWhere('start_page', 'like', "%{$search}%")
                ->orWhere('end_page', 'like', "%{$search}%")

                // Subject search
                ->orWhereHas('school_subject', function ($sub) use ($search) {
                    $sub->where('subject_name', 'like', "%{$search}%");
                })

                // Session search
                ->orWhereHas('school_session', function ($sess) use ($search) {
                    $sess->where('session_year', 'like', "%{$search}%");
                })

                // Class search
                ->orWhereHas('school_class', function ($cls) use ($search) {
                    $cls->where('class_name', 'like', "%{$search}%");
                })

                // Group search
                ->orWhereHas('school_group', function ($grp) use ($search) {
                    $grp->where('group_name', 'like', "%{$search}%");
                })

                // Section search
                ->orWhereHas('school_section', function ($sec) use ($search) {
                    $sec->where('section_name', 'like', "%{$search}%");
                });
            });
        }

        $syllabuses = $query->latest()->paginate(10);

        if ($syllabuses->total() == 0) {
            return response()->json([
                'message' => 'No syllabus found for "' . $request->search . '"',
                'data' => [],
                'total' => 0
            ]);
        }

        return response()->json($syllabuses);
    }

}