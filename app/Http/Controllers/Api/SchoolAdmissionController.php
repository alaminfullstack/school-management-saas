<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guardian;
use App\Models\AdmissionStudent;
use App\Models\SchoolClass;
use App\Models\School;
use App\Services\SmsService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SchoolAdmissionController extends Controller
{
    protected $smsService;

    /**
     * Inject the SmsService
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function register(Request $request)
    {
        // 1. Validation
        try {
            $request->validate([
                'a_class' => 'required|exists:school_classes,id',
                'a_section' => 'required|exists:school_sections,id',
                'a_session' => 'required|exists:school_sessions,id',
                'a_group' => 'nullable|exists:school_groups,id',
                'a_fee' => 'required',
                'a_date' => 'required|date',
                'g_name'     => 'nullable',
                'g_relation' => 'nullable',
                'g_mobile'   => 'nullable',
                'g_division' => 'nullable',
                'g_district' => 'nullable',
                'g_upazila'  => 'nullable',
                'g_village'  => 'nullable',
                'student_name' => 'required|string|max:255',
                'father_name' => 'required|string|max:255',
                'mother_name' => 'required|string|max:255',
                'student_mobile' => 'required|unique:users,mobile|unique:admission_students,mobile',
                'password' => 'required|confirmed|min:6',
                'image' => 'nullable|image|max:2048',
                'current_division' => 'required',
                'current_district' => 'required',
                'current_upazila' => 'required',
                'current_village' => 'required',
                'permanent_division' => 'required',
                'permanent_district' => 'required',
                'permanent_upazila' => 'required',
                'permanent_village' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            // Use DB Transaction to ensure data integrity
            $result = DB::transaction(function () use ($request) {

                $currentUser = Auth::user();

                // FORCE schoolId to be the logged-in User's ID
                $schoolId = $currentUser->id;
                $schoolName = $currentUser->school_name;

                if (!$schoolId) {
                    throw new \Exception("Authentication error: School ID not found.");
                }

                // --- CONVERT IDs TO ACTUAL VALUES FOR SMS/LOGS ONLY ---
                $classObj = SchoolClass::find($request->a_class);
                $classNameForSms = $classObj ? $classObj->class_name : 'N/A';

                // ================= FIXED STUDENT ID GENERATION LOGIC =================
                // 1. Get the last 4 digits of the school's own ID number as the prefix
                $schoolPrefix = substr($currentUser->id_number, -4);

                // 2. Find the student with the highest ID number for this specific school
                $lastStudentUser = User::where('school_name', $schoolName)
                    ->where('role', 'student')
                    ->where('id_number', 'LIKE', $schoolPrefix . '%')
                    ->orderBy('id_number', 'desc')
                    ->first();

                if (!$lastStudentUser) {
                    // 3. Start from 1 if no student exists yet
                    $nextStudentNumber = 1;
                } else {
                    // 4. Extract the numeric suffix and increment it
                    // Using preg_replace to ensure we only get the trailing digits if needed, 
                    // or simply substr if your ID format is strictly (Prefix)(4-digit-suffix)
                    $lastStudentIdSuffix = (int) substr($lastStudentUser->id_number, -4);
                    $nextStudentNumber = $lastStudentIdSuffix + 1;
                }

                // 5. Format to 4 digits (e.g., 1 becomes 0001) and prepend prefix
                $formattedStudentId = $schoolPrefix . str_pad($nextStudentNumber, 4, '0', STR_PAD_LEFT);
                // =====================================================================

                // 3. Create Guardian
                $guardian = Guardian::create([
                    'name' => $request->g_name ?? 'N/A',
                    'relation' => $request->g_relation ?? 'N/A',
                    'division' => $request->g_division ?? 'N/A',
                    'district' => $request->g_district ?? 'N/A',
                    'upazila' => $request->g_upazila ?? 'N/A',
                    'village' => $request->g_village ?? 'N/A',
                    'mobile' => $request->g_mobile ?? 'N/A',
                ]);

                // 4. Handle Image Upload
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('students', 'public');
                }

                // 5. Save Admission Student Record (STORING IDs INSTEAD OF NAMES)
                $admission = AdmissionStudent::create([
                    'school_id' => $schoolId,
                    'school' => $schoolName,
                    'class' => $request->a_class,      // Storing ID
                    'group' => $request->a_group,      // Storing ID
                    'section' => $request->a_section,  // Storing ID
                    'session' => $request->a_session,  // Storing ID
                    'admission_fee' => $request->a_fee,
                    'admission_date' => $request->a_date,
                    'previous_school' => $request->p_school ?? 'n/a',
                    'previous_class' => $request->p_class ?? 'n/a',
                    'previous_group' => $request->p_group ?? 'n/a',
                    'previous_section' => $request->p_section ?? 'n/a',
                    'previous_session' => $request->p_session ?? 'n/a',
                    'last_exam_result' => $request->last_exam_result ?? 'n/a',
                    'guardian_id' => $guardian->id,
                    'student_name' => $request->student_name,
                    'father_name' => $request->father_name,
                    'mother_name' => $request->mother_name,
                    //'student_id_number' => $formattedStudentId,
                    'mobile' => $request->student_mobile,
                    'password' => Hash::make($request->password),
                    'image' => $imagePath,
                    'current_division' => $request->current_division,
                    'current_district' => $request->current_district,
                    'current_upazila' => $request->current_upazila,
                    'current_village' => $request->current_village,
                    'permanent_division' => $request->permanent_division,
                    'permanent_district' => $request->permanent_district,
                    'permanent_upazila' => $request->permanent_upazila,
                    'permanent_village' => $request->permanent_village,
                    'status' => 'approved',
                ]);

                // 6. Create Student User Account
                User::create([
                    'role' => 'student',
                    'name' => $request->student_name,
                    'school_name' => $schoolName,
                    'mobile' => $request->student_mobile,
                    'id_number' => $admission->student_id_number ?? '',
                    'password' => Hash::make($request->password),
                ]);

                return [
                    'student_mobile' => $request->student_mobile,
                    'school_name'    => $schoolName,
                    'student_id'     => $admission->student_id_number ?? '',
                    'school_id'      => $schoolId
                ];
            });

            // 7. Send SMS using Service
            $this->sendAdmissionSMS($result['student_mobile'], $result['school_name'], $result['student_id'], $result['school_id']);

            return response()->json([
                'message' => 'Student registered and approved successfully! ID: ' . $result['student_id'],
                'redirect' => '/school/students'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Admission Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during registration.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper Method: Uses SmsService to send notification
     */
    private function sendAdmissionSMS($mobile, $schoolName, $studentId, $schoolUserId)
    {
        $school = School::where('user_id', $schoolUserId)->first();

        if (!$school) {
            Log::error("Admission SMS failed: School record not found for user ID $schoolUserId");
            return;
        }

        $message = "Welcome! Your admission at $schoolName is confirmed. Student ID: $studentId. You can now login to your portal. Regards, $schoolName.";

        $smsResponse = $this->smsService->sendSms($school, $mobile, $message);

        if (!$smsResponse['success']) {
            Log::warning("Admission SMS for $mobile failed: " . $smsResponse['message']);
        }
    }
}