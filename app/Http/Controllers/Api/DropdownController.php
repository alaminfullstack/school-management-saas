<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolExamName;
use App\Models\SchoolGroup;
use App\Models\SchoolSection;
use App\Models\SchoolSession;
use App\Models\SchoolSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DropdownController extends Controller
{
    /**
     * Helper to get the school instance for the logged-in user.
     */
    private function getSchool()
    {
        return School::where('user_id', Auth::id())->first();
    }

    public function getSchoolInfo()
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['message' => 'School not found'], 404);

        return response()->json(['data' => $school]);
    }

    public function getClasses()
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['data' => []], 404);

        $data = SchoolClass::where('school_id', $school->id)->get();
        return response()->json(['data' => $data]);
    }

    public function getGroups(Request $request)
    {
        $school = $this->getSchool();
        $query = SchoolGroup::where('school_id', $school->id);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function getSections(Request $request)
    {
        $school = $this->getSchool();
        $query = SchoolSection::where('school_id', $school->id);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        return response()->json(['data' => $query->get()]);
    }

    public function getSessions(Request $request)
    {
        $school = $this->getSchool();
        if (!$school) return response()->json(['data' => []], 404);

        $query = SchoolSession::where('school_id', $school->id);

        // Mandate class_id filtering
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        } else {
            // If no class is selected, return empty to prevent showing all sessions
            return response()->json(['data' => []]);
        }

        // Filter by group_id (if provided, otherwise filter for null/none)
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Filter by section_id (if provided)
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * Get Subjects filtered by class/group/section
     */
    public function getSubjects(Request $request)
    {
        $school = $this->getSchool();
        $query = SchoolSubject::where('school_id', $school->id);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * Get Exam Names filtered by class, group, section, and session
     */
    public function getExams(Request $request)
    {
        $school = $this->getSchool();

        $query = SchoolExamName::where('school_id', $school->id);

        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }
        if ($request->filled('group_name')) {
            $query->where('group_name', $request->group_name);
        }
        if ($request->filled('section_name')) {
            $query->where('section_name', $request->section_name);
        }
        if ($request->filled('session_name')) {
            $query->where('session_name', $request->session_name);
        }

        $exams = $query->get();

        return response()->json([
            'data' => $exams
        ]);
    }

    public function getStudents(Request $request)
    {
        try {
            // Fetch the logged-in User ID which acts as the school_id in admission_students
            $currentSchoolUserId = Auth::id();

            if (!$currentSchoolUserId) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Build the query focusing strictly on the authenticated school
            $query = DB::table('admission_students')
                ->where('school_id', $currentSchoolUserId);

            // Apply dynamic filters based on the request
            if ($request->filled('class_id')) {
                $query->where('class', $request->class_id);
            }
            if ($request->filled('session_id')) {
                $query->where('session', $request->session_id);
            }
            if ($request->filled('group_id')) {
                $query->where('group', $request->group_id);
            }
            if ($request->filled('section_id')) {
                $query->where('section', $request->section_id);
            }

            // Return only the necessary fields for the dropdown
            $students = $query->select('id', 'student_name', 'student_id_number')
                ->orderBy('student_name', 'asc')
                ->get();

            return response()->json([
                'data' => $students
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch students.'
            ], 500);
        }
    }
}
