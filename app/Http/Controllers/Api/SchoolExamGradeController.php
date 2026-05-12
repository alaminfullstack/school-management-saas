<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolExamGrade;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolExamGradeController extends Controller
{
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    public function index(Request $request)
    {
        $school = $this->getSchool();
        if (!$school) {
            return response()->json(['data' => [], 'message' => 'School not found'], 404);
        }

        $query = SchoolExamGrade::where('school_id', $school->id);

        // --- Multi-Column Search Implementation ---
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('class_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('subject_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('letter_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('group_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('section_name', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Advanced Filtering
        if ($request->filled('class_name')) $query->where('class_name', $request->class_name);
        if ($request->filled('group_name')) $query->where('group_name', $request->group_name);
        if ($request->filled('section_name')) $query->where('section_name', $request->section_name);
        if ($request->filled('subject_name')) $query->where('subject_name', $request->subject_name);

        $grades = $query->orderBy('class_name', 'asc')
            ->orderBy('max_mark', 'desc')
            ->paginate($request->per_page ?? 10);

        return response()->json($grades);
    }

    public function store(Request $request)
    {
        $school = $this->getSchool();

        $request->validate([
            'class_name'    => 'required|string',
            'subject_name'  => 'required|string',
            'grades'        => 'required|array|min:1',
            'grades.*.min_mark'     => 'required|numeric',
            'grades.*.max_mark'     => 'required|numeric',
            'grades.*.number_point' => 'required|numeric', // Updated field
            'grades.*.letter_name'  => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->grades as $gradeData) {
                // Strict Unique Check per row
                $exists = SchoolExamGrade::where([
                    'school_id'    => $school->id,
                    'class_name'   => $request->class_name,
                    'group_name'   => $request->group_name,
                    'section_name' => $request->section_name,
                    'subject_name' => $request->subject_name,
                    'letter_name'  => $gradeData['letter_name'],
                ])->exists();

                if ($exists) continue; // Skip duplicates in bulk upload

                SchoolExamGrade::create([
                    'school_id'    => $school->id,
                    'class_name'   => $request->class_name,
                    'group_name'   => $request->group_name,
                    'section_name' => $request->section_name,
                    'subject_name' => $request->subject_name,
                    'min_mark'     => $gradeData['min_mark'],
                    'max_mark'     => $gradeData['max_mark'],
                    'number_point' => $gradeData['number_point'], // Direct mapping
                    'letter_name'  => $gradeData['letter_name'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Exam Grades created successfully']);
        } catch (\Throwable $error) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $error->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $school = $this->getSchool();
        $grade = SchoolExamGrade::where('school_id', $school->id)->findOrFail($id);
        return response()->json($grade);
    }

    public function update(Request $request, $id)
    {
        $school = $this->getSchool();
        $grade = SchoolExamGrade::where('school_id', $school->id)->findOrFail($id);

        // Handle both single-object and array-wrapped updates from the frontend
        $gradeData = isset($request->grades[0]) ? $request->grades[0] : $request->all();

        // Check conflicts excluding self
        $exists = SchoolExamGrade::where([
            'school_id'    => $school->id,
            'class_name'   => $request->class_name,
            'group_name'   => $request->group_name,
            'section_name' => $request->section_name,
            'subject_name' => $request->subject_name,
            'letter_name'  => $gradeData['letter_name'],
        ])->where('id', '!=', $id)->exists();

        if ($exists) {
            return response()->json(['message' => 'Conflict: This grade configuration already exists.'], 422);
        }

        $grade->update([
            'class_name'   => $request->class_name,
            'group_name'   => $request->group_name,
            'section_name' => $request->section_name,
            'subject_name' => $request->subject_name,
            'min_mark'     => $gradeData['min_mark'] ?? $grade->min_mark,
            'max_mark'     => $gradeData['max_mark'] ?? $grade->max_mark,
            'number_point' => $gradeData['number_point'] ?? $grade->number_point, // Updated
            'letter_name'  => $gradeData['letter_name'] ?? $grade->letter_name,
        ]);

        return response()->json(['message' => 'Grade updated successfully']);
    }

    public function destroy($id)
    {
        $school = $this->getSchool();
        SchoolExamGrade::where('school_id', $school->id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Grade deleted successfully']);
    }
}
