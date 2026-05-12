<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolSubjectController extends Controller
{
    public function index(Request $request)
    {
        // Find the school associated with this user
        $school = School::where('user_id', Auth::id())->firstOrFail();

        $query = SchoolSubject::where('school_id', $school->id)
            ->with(['school_class', 'school_group', 'school_section']);

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

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('subject_name', 'like', "%{$request->search}%")
                    ->orWhereHas('school_class', function ($subQ) use ($request) {
                        $subQ->where('class_name', 'like', "%{$request->search}%");
                    });
            });
        }

        return $query->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'class_id' => 'required',
            'subject_name' => 'required|string',
        ]);

        // Merge school_id into the data to ensure tenant isolation
        $data = array_merge($request->all(), ['school_id' => $school->id]);

        return SchoolSubject::create($data);
    }

    public function show($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        // Ensure user can only view a subject belonging to their school
        return SchoolSubject::where('school_id', $school->id)->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();
        $subject = SchoolSubject::where('school_id', $school->id)->findOrFail($id);

        $request->validate([
            'class_id' => 'required',
            'subject_name' => 'required|string',
        ]);

        $subject->update($request->all());
        return $subject;
    }

    public function destroy($id)
    {
        $school = School::where('user_id', Auth::id())->firstOrFail();

        // Ensure user can only delete a subject belonging to their school
        $subject = SchoolSubject::where('school_id', $school->id)->findOrFail($id);

        $subject->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
    
    // School Subject Filter
    public function subjectFilter(Request $request)
    {
        // Find authenticated user's school
        $school = School::where('user_id', Auth::id())->first();

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.',
                'data' => []
            ], 404);
        }

        $query = SchoolSubject::where('school_id', $school->id)
            ->with([
                'school_class',
                'school_group',
                'school_section'
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                // Subject name search
                $q->where('subject_name', 'like', "%{$search}%")

                // Class name search
                ->orWhereHas('school_class', function ($sq) use ($search) {
                    $sq->where('class_name', 'like', "%{$search}%");
                })

                // Group name search
                ->orWhereHas('school_group', function ($gq) use ($search) {
                    $gq->where('group_name', 'like', "%{$search}%");
                })

                // Section name search
                ->orWhereHas('school_section', function ($secQ) use ($search) {
                    $secQ->where('section_name', 'like', "%{$search}%");
                });
            });
        }

        $subjects = $query->latest()->paginate(10);

        // No data found message
        if ($subjects->total() == 0) {
            return response()->json([
                'message' => 'No subject found for "' . $request->search . '"',
                'data' => [],
                'total' => 0
            ]);
        }

        return response()->json($subjects);
    }
}