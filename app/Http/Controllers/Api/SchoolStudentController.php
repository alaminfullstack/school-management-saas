<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdmissionStudent;
use App\Models\User;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SchoolStudentController extends Controller
{
    /**
     * Fetch all students (Internal & Outside Registration)
     */
    public function index(Request $request)
    {
        $schoolId = Auth::id();
        $schoolName = Auth::user()->school_name;

        $query = AdmissionStudent::with([
            'schoolClass',
            'schoolSection',
            'schoolGroup',
            'schoolSession'
        ])->where(function ($q) use ($schoolName, $schoolId) {
            $q->where('school_id', $schoolId)
                ->orWhere('school', $schoolName);
        });

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%$search%")
                    ->orWhere('student_id_number', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        }

        // --- Direct Column Filters (String Names) ---
        if ($request->filled('class')) {
            $query->where('class', $request->class);
        }
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }
        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }
        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        // When `all=true` is passed (e.g. for quick-search dropdowns), return all records unpaginated
        if ($request->boolean('all')) {
            $students = $query->orderBy('id', 'desc')->get()->map(function ($student) {
                $student->class_name   = $student->schoolClass->class_name   ?? $student->class;
                $student->section_name = $student->schoolSection->section_name ?? $student->section;
                $student->group_name   = $student->schoolGroup->group_name   ?? $student->group;
                $student->session_year = $student->schoolSession->session_year ?? $student->session;
                return $student;
            });

            return response()->json($students);
        }

        $students = $query->orderBy('id', 'desc')->paginate(10);

        // Map names to the response for display consistency
        $students->getCollection()->transform(function ($student) {
            $student->class_name   = $student->schoolClass->class_name   ?? $student->class;
            $student->section_name = $student->schoolSection->section_name ?? $student->section;
            $student->group_name   = $student->schoolGroup->group_name   ?? $student->group;
            $student->session_year = $student->schoolSession->session_year ?? $student->session;
            return $student;
        });

        return response()->json($students);
    }

    /**
     * Update Approval Status (Approve/Reject)
     */
    public function updateStatus(Request $request, $id)
    {
        $schoolName = Auth::user()->school_name;
        $schoolId = Auth::id();

        $student = AdmissionStudent::where(function ($q) use ($schoolName, $schoolId) {
            $q->where('school', $schoolName)->orWhere('school_id', $schoolId);
        })->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student->status = $request->status;
        $student->save();

        return response()->json([
            'message' => 'Student status updated to ' . $request->status,
            'status' => $student->status
        ]);
    }

    /**
     * Standard show for Quick Edit Modal
     * Returns only the student record
     */
    public function show($id)
    {
        $schoolId = Auth::id();
        $student = AdmissionStudent::with([
            'schoolClass',
            'schoolSection',
            'schoolGroup',
            'schoolSession'
        ])->where('school_id', $schoolId)->findOrFail($id);

        // Map names for the standard edit inputs
        $student->class_name = $student->schoolClass->class_name ?? $student->class;
        $student->section_name = $student->schoolSection->section_name ?? $student->section;
        $student->group_name = $student->schoolGroup->group_name ?? $student->group;
        $student->session_year = $student->schoolSession->session_year ?? $student->session;

        return response()->json($student);
    }

    /**
     * Comprehensive show for Profile Details Modal
     * Returns student + guardian data
     */
    public function showDetails($id)
    {
        $schoolId = Auth::id();

        $student = AdmissionStudent::with([
            'schoolClass',
            'schoolSection',
            'schoolGroup',
            'schoolSession'
        ])->where('school_id', $schoolId)->findOrFail($id);

        // Map helper names
        $student->class_name = $student->schoolClass->class_name ?? $student->class;
        $student->section_name = $student->schoolSection->section_name ?? $student->section;
        $student->group_name = $student->schoolGroup->group_name ?? $student->group;
        $student->session_year = $student->schoolSession->session_year ?? $student->session;

        $guardian = DB::table('guardians')->where('id', $student->guardian_id)->first();

        return response()->json([
            'student' => $student,
            'guardian' => $guardian
        ]);
    }

    public function updateDetails(Request $request, $id)
    {
        $student = AdmissionStudent::where('school_id', Auth::id())->findOrFail($id);
        $data = $request->except(['id', 'school_id', 'image', 'password']);
        $student->update($data);

        return response()->json(['message' => 'Profile Updated Successfully']);
    }


    /**
     * Update student details (Modified for Section/Group IDs)
     */
    public function update(Request $request, $id)
    {
        $schoolName = Auth::user()->school_name;
        $schoolId = Auth::id();

        $student = AdmissionStudent::where(function ($q) use ($schoolName, $schoolId) {
            $q->where('school', $schoolName)->orWhere('school_id', $schoolId);
        })->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'student_name'      => 'required|string|max:255',
            'father_name'       => 'required|string|max:255',
            'mother_name'       => 'required|string|max:255',
            'mobile'            => 'required|string|max:20',
            'class'             => 'required|exists:school_classes,id',
            'section'           => 'required|exists:school_sections,id',
            'session'           => 'required|exists:school_sessions,id',
            'group'             => 'nullable|exists:school_groups,id',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'admission_fee'     => 'required',
            'admission_date'    => 'required|date',
            'previous_school'   => 'nullable|string|max:255',
            'previous_class'    => 'nullable|string|max:100',
            'previous_group'    => 'nullable|string|max:100',
            'previous_section'  => 'nullable|string|max:100',
            'previous_session'  => 'nullable|string|max:100',
            'last_exam_result'  => 'nullable|string|max:100',
            'current_division'  => 'required|string|max:100',
            'current_district'  => 'required|string|max:100',
            'current_upazila'   => 'required|string|max:100',
            'current_village'   => 'required|string|max:255',
            'permanent_division' => 'required|string|max:100',
            'permanent_district' => 'required|string|max:100',
            'permanent_upazila'  => 'required|string|max:100',
            'permanent_village'  => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Basic Info
        $student->student_name = $request->student_name;
        $student->father_name = $request->father_name;
        $student->mother_name = $request->mother_name;
        $student->mobile = $request->mobile;

        // Academic Info (Storing IDs)
        $student->class = $request->class;
        $student->session = $request->session;
        $student->section = $request->section;
        $student->group = $request->group;

        // Admission info
        if ($request->filled('admission_fee'))  $student->admission_fee  = $request->admission_fee;
        if ($request->filled('admission_date')) $student->admission_date = $request->admission_date;

        // Previous school
        if ($request->has('previous_school'))   $student->previous_school   = $request->previous_school;
        if ($request->has('previous_class'))    $student->previous_class    = $request->previous_class;
        if ($request->has('previous_group'))    $student->previous_group    = $request->previous_group;
        if ($request->has('previous_section'))  $student->previous_section  = $request->previous_section;
        if ($request->has('previous_session'))  $student->previous_session  = $request->previous_session;
        if ($request->has('last_exam_result'))  $student->last_exam_result  = $request->last_exam_result;

        // Address
        if ($request->has('current_division'))   $student->current_division   = $request->current_division;
        if ($request->has('current_district'))   $student->current_district   = $request->current_district;
        if ($request->has('current_upazila'))    $student->current_upazila    = $request->current_upazila;
        if ($request->has('current_village'))    $student->current_village    = $request->current_village;
        if ($request->has('permanent_division')) $student->permanent_division = $request->permanent_division;
        if ($request->has('permanent_district')) $student->permanent_district = $request->permanent_district;
        if ($request->has('permanent_upazila'))  $student->permanent_upazila  = $request->permanent_upazila;
        if ($request->has('permanent_village'))  $student->permanent_village  = $request->permanent_village;

        // Image Handling
        if ($request->hasFile('image')) {
            if ($student->image && file_exists(public_path('storage/' . $student->image))) {
                unlink(public_path('storage/' . $student->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/students'), $filename);
            $student->image = 'students/' . $filename;
        }

        $student->save();

        return response()->json(['message' => 'Student updated successfully']);
    }

    /**
     * Total Delete
     */
    public function destroy($id)
    {
        $schoolName = Auth::user()->school_name;

        $student = AdmissionStudent::where('school', $schoolName)->findOrFail($id);

        try {
            DB::transaction(function () use ($student, $schoolName) {

                User::where('school_name', $schoolName)
                    ->where('id_number', $student->student_id_number)
                    ->where('role', 'student')
                    ->delete();

                if ($student->image && file_exists(public_path('storage/' . $student->image))) {
                    unlink(public_path('storage/' . $student->image));
                }

                $guardianId = $student->guardian_id;
                $student->delete();

                if ($guardianId) {
                    $hasOtherStudents = AdmissionStudent::where('guardian_id', $guardianId)->exists();
                    if (!$hasOtherStudents) {
                        Guardian::where('id', $guardianId)->delete();
                    }
                }
            });

            return response()->json(['message' => 'Student and associated accounts removed successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete student data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}