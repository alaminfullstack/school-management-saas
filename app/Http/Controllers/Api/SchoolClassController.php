<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolClassController extends Controller
{
    /**
     * Helper to get school ID
     */
    private function getSchoolId($user)
    {
        return DB::table('schools')->where('user_id', $user->id)->value('id');
    }

    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId($request->user());

        if (!$schoolId) {
            return response()->json(['data' => [], 'total' => 0]);
        }

        $query = SchoolClass::where('school_id', $schoolId);

        if ($request->filled('search')) {
            $query->where('class_name', 'like', "%{$request->search}%");
        }

        return response()->json($query->orderBy('class_name', 'asc')->paginate(10));
    }

    public function store(Request $request)
    {
        $schoolId = $this->getSchoolId($request->user());

        if (!$schoolId) {
            return response()->json(['message' => 'School profile not found.'], 404);
        }

        $request->validate([
            'class_name' => 'required|string|max:255',
        ]);

        // Check if class already exists for THIS school only
        $exists = SchoolClass::where('school_id', $schoolId)
            ->where('class_name', $request->class_name)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This class name already exists.'], 422);
        }

        $record = SchoolClass::create([
            'school_id' => $schoolId,
            'class_name' => $request->class_name,
        ]);

        return response()->json(['message' => 'Record created successfully', 'data' => $record]);
    }

    public function show(Request $request, $id)
    {
        $schoolId = $this->getSchoolId($request->user());
        return response()->json(SchoolClass::where('school_id', $schoolId)->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $schoolId = $this->getSchoolId($request->user());

        $request->validate([
            'class_name' => 'required|string|max:255',
        ]);

        $record = SchoolClass::where('school_id', $schoolId)->findOrFail($id);

        // Check for duplicate names excluding the current record
        $exists = SchoolClass::where('school_id', $schoolId)
            ->where('class_name', $request->class_name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Another class already has this name.'], 422);
        }

        $record->update(['class_name' => $request->class_name]);

        return response()->json(['message' => 'Record updated successfully']);
    }

    public function destroy(Request $request, $id)
    {
        $schoolId = $this->getSchoolId($request->user());
        SchoolClass::where('school_id', $schoolId)->findOrFail($id)->delete();

        return response()->json(['message' => 'Record deleted']);
    }
    
        //filter by school
    public function classFilter(Request $request)
    {
        $schoolId = $this->getSchoolId($request->user());

        if (!$schoolId) {
            return response()->json([
                'message' => 'School profile not found.'
            ], 404);
        }

        $query = SchoolClass::where('school_id', $schoolId);

        if ($request->filled('search')) {
            $query->where('class_name', 'like', '%' . $request->search . '%');
        }

        $classes = $query->latest()->paginate(10);

        return response()->json($classes);
    }
    
    
}