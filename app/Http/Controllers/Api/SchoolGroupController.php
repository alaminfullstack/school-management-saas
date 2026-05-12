<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolGroupController extends Controller
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

        $query = SchoolGroup::with('schoolClass')
            ->where('school_id', $school->id);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('group_name', 'like', "%{$request->search}%")
                    ->orWhereHas('schoolClass', function ($sq) use ($request) {
                        $sq->where('class_name', 'like', "%{$request->search}%");
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
            'class_id'   => 'required|exists:school_classes,id',
            'group_name' => 'required|string|max:255'
        ]);

        // Security check: Ensure class belongs to school
        $classExists = DB::table('school_classes')
            ->where('id', $request->class_id)
            ->where('school_id', $school->id)
            ->exists();

        if (!$classExists) {
            return response()->json(['message' => 'Invalid class selection.'], 403);
        }

        // Duplicate Check
        $duplicate = SchoolGroup::where('school_id', $school->id)
            ->where('class_id', $request->class_id)
            ->where('group_name', $request->group_name)
            ->exists();

        if ($duplicate) {
            return response()->json(['message' => 'This Group already exists for this class.'], 422);
        }

        $group = SchoolGroup::create([
            'school_id'  => $school->id,
            'class_id'   => $request->class_id,
            'group_name' => $request->group_name,
        ]);

        return response()->json(['message' => 'Group created successfully', 'data' => $group]);
    }

    public function show(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['message' => 'Unauthorized'], 401);

        $group = SchoolGroup::where('school_id', $school->id)->findOrFail($id);

        return response()->json($group);
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['message' => 'Unauthorized'], 401);

        $group = SchoolGroup::where('school_id', $school->id)->findOrFail($id);

        $request->validate([
            'class_id'   => 'required|exists:school_classes,id',
            'group_name' => 'required|string|max:255'
        ]);

        $classExists = DB::table('school_classes')
            ->where('id', $request->class_id)
            ->where('school_id', $school->id)
            ->exists();

        if (!$classExists) {
            return response()->json(['message' => 'Invalid class selection.'], 403);
        }

        // Duplicate Check (Excluding current ID)
        $duplicate = SchoolGroup::where('school_id', $school->id)
            ->where('class_id', $request->class_id)
            ->where('group_name', $request->group_name)
            ->where('id', '!=', $id)
            ->exists();

        if ($duplicate) {
            return response()->json(['message' => 'This Group already exists for this class.'], 422);
        }

        $group->update([
            'class_id'   => $request->class_id,
            'group_name' => $request->group_name,
        ]);

        return response()->json(['message' => 'Group updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        $school = $this->getSchool($request->user());
        if (!$school) return response()->json(['message' => 'Unauthorized'], 401);

        $group = SchoolGroup::where('school_id', $school->id)->findOrFail($id);
        $group->delete();

        return response()->json(['message' => 'Group deleted successfully']);
    }
    
        // School group filter
    public function groupFilter(Request $request)
    {
        $school = $this->getSchool($request->user());

        if (!$school) {
            return response()->json([
                'message' => 'School profile not found.'
            ], 404);
        }

        $query = SchoolGroup::with('schoolClass')
            ->where('school_id', $school->id);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('group_name', 'like', "%{$search}%")
                ->orWhereHas('schoolClass', function ($sq) use ($search) {
                    $sq->where('class_name', 'like', "%{$search}%");
                });
            });
        }

        $groups = $query->latest()->paginate(10);

        return response()->json($groups);
    }
    
    
    
}