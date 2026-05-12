<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExamName;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolExamNameController extends Controller
{
    /**
     * Helper to get the school ID associated with the authenticated user.
     */
    private function getSchoolId()
    {
        return School::where('user_id', Auth::id())->first()->id;
    }

    public function index(Request $request)
    {
        $query = SchoolExamName::where('school_id', $this->getSchoolId());

        // Text Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('exam_name', 'like', '%' . $search . '%')
                    ->orWhere('class_name', 'like', '%' . $search . '%')
                    ->orWhere('section_name', 'like', '%' . $search . '%')
                    ->orWhere('session_name', 'like', '%' . $search . '%');
            });
        }

        // Advanced Filters
        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }
        if ($request->filled('section_name')) {
            $query->where('section_name', $request->section_name);
        }
        if ($request->filled('group_name')) {
            $query->where('group_name', $request->group_name);
        }
        if ($request->filled('session_name')) {
            $query->where('session_name', $request->session_name);
        }

        return $query->orderBy('id', 'desc')->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_name'   => 'required|string',
            'section_name' => 'required|string',
            'group_name'   => 'required|string',
            'session_name' => 'required|string',
            'exam_name'    => 'required|string|max:255',
        ]);

        $data = $request->only([
            'class_name',
            'section_name',
            'group_name',
            'session_name',
            'exam_name'
        ]);

        $data['school_id'] = $this->getSchoolId();

        $exam = SchoolExamName::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Exam name created successfully',
            'data'    => $exam
        ], 201);
    }

    public function show($id)
    {
        return SchoolExamName::where('school_id', $this->getSchoolId())->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $exam = SchoolExamName::where('school_id', $this->getSchoolId())->findOrFail($id);

        $request->validate([
            'class_name'   => 'required|string',
            'section_name' => 'required|string',
            'group_name'   => 'required|string',
            'session_name' => 'required|string',
            'exam_name'    => 'required|string|max:255',
        ]);

        $exam->update($request->only([
            'class_name',
            'section_name',
            'group_name',
            'session_name',
            'exam_name'
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Exam name updated successfully',
            'data'    => $exam
        ]);
    }

    public function destroy($id)
    {
        $exam = SchoolExamName::where('school_id', $this->getSchoolId())->findOrFail($id);
        $exam->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Deleted successfully'
        ]);
    }
}
