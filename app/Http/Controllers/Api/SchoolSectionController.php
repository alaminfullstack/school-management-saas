<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolSectionController extends Controller
{
    /**
     * Helper to get the school associated with the authenticated user.
     */
    private function getSchool($user)
    {
        return DB::table('schools')->where('user_id', $user->id)->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $query = SchoolSection::with(['schoolClass', 'schoolGroup'])
            ->where('school_id', $school->id);

        // Filter by class_id
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by group_id
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Filter by section name
        if ($request->filled('section_name')) {
            $query->where('section_name', 'like', "%{$request->section_name}%");
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('section_name', 'like', "%{$request->search}%")
                    ->orWhereHas('schoolClass', function ($sq) use ($request) {
                        $sq->where('class_name', 'like', "%{$request->search}%");
                    })
                    ->orWhereHas('schoolGroup', function ($sgq) use ($request) {
                        $sgq->where('group_name', 'like', "%{$request->search}%");
                    });
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
            'class_id'     => 'required|exists:school_classes,id',
            'group_id'     => 'nullable|exists:school_groups,id',
            'section_name' => 'required|string|max:255',
        ]);

        // Duplicate Check
        $exists = SchoolSection::where('school_id', $school->id)
            ->where('class_id', $request->class_id)
            ->where('group_id', $request->group_id)
            ->where('section_name', $request->section_name)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This section already exists for the selected class and group.'
            ], 422);
        }

        $section = SchoolSection::create([
            'school_id'    => $school->id,
            'class_id'     => $request->class_id,
            'group_id'     => $request->group_id,
            'section_name' => $request->section_name,
        ]);

        return response()->json(['message' => 'Section created successfully', 'data' => $section]);
    }

    public function show(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $section = SchoolSection::where('school_id', $school->id)->findOrFail($id);

        return response()->json($section);
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $section = SchoolSection::where('school_id', $school->id)->findOrFail($id);

        $request->validate([
            'class_id'     => 'required|exists:school_classes,id',
            'group_id'     => 'nullable|exists:school_groups,id',
            'section_name' => 'required|string|max:255',
        ]);

        // Duplicate Check (excluding current record)
        $exists = SchoolSection::where('school_id', $school->id)
            ->where('class_id', $request->class_id)
            ->where('group_id', $request->group_id)
            ->where('section_name', $request->section_name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Another section with this name already exists for the selected class.'
            ], 422);
        }

        $section->update([
            'class_id'     => $request->class_id,
            'group_id'     => $request->group_id,
            'section_name' => $request->section_name,
        ]);

        return response()->json(['message' => 'Section updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        $section = SchoolSection::where('school_id', $school->id)->findOrFail($id);

        $section->delete();

        return response()->json(['message' => 'Section deleted successfully']);
    }
    
        //School section filter
    public function sectionFilter(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.'
            ], 404);
        }

        $query = SchoolSection::with(['schoolClass', 'schoolGroup'])
            ->where('school_id', $school->id);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('section_name', 'like', "%{$search}%")
                ->orWhereHas('schoolClass', function ($sq) use ($search) {
                    $sq->where('class_name', 'like', "%{$search}%");
                })
                ->orWhereHas('schoolGroup', function ($sg) use ($search) {
                    $sg->where('group_name', 'like', "%{$search}%");
                });
            });
        }

        $sections = $query->latest()->paginate(10);

        return response()->json($sections);
    }

    
    
    
}