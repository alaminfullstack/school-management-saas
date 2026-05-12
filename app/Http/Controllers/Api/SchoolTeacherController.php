<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\School;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SchoolTeacherController extends Controller
{
    protected $smsService;

    /**
     * Inject the SmsService
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    // List teachers with Pagination
    public function index(Request $request)
    {
        $school = School::where('user_id', $request->user()->id)->first();
        if (!$school) {
            return response()->json(['message' => 'Invalid user school profile'], 400);
        }
        $schoolId = $school->id;

        $query = Teacher::where('school_id', $schoolId);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('designation', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('mobile', 'like', "%$search%");
            });
        }

        if ($request->filled('designation')) {
            $query->where('designation', $request->designation);
        }

        $teachers = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($teachers);
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $school = School::where('user_id', $currentUser->id)->first();

        if (!$school) {
            return response()->json(['message' => 'Invalid school profile'], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'mobile' => 'required|string|unique:teachers,mobile|unique:users,mobile',
            'email' => 'required|email|unique:teachers,email',
            'password' => 'required|string|min:6|confirmed',
            'dob' => 'nullable|date',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = DB::transaction(function () use ($request, $school, $currentUser) {

                // ================= FIXED ID GENERATION LOGIC (Schema Matched) =================
                $schoolPrefix = substr($currentUser->id_number, -4);

                // We use school_name to find the last teacher because users table lacks school_id
                $lastTeacherUser = User::where('school_name', $school->school_name)
                    ->where('role', 'teacher')
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$lastTeacherUser) {
                    $nextTeacherNumber = 1501;
                } else {
                    $lastIdSuffix = (int) substr($lastTeacherUser->id_number, -4);
                    $nextTeacherNumber = $lastIdSuffix + 1;
                }

                $formattedTeacherId = $schoolPrefix . str_pad($nextTeacherNumber, 4, '0', STR_PAD_LEFT);
                // =============================================================================

                $photoPath = null;
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')->store('teacher_photos', 'public');
                }

                // Create Teacher record
                $teacher = Teacher::create([
                    'school_id' => $school->id,
                    'name' => $request->name,
                    'designation' => $request->designation,
                    'id_number' => $formattedTeacherId,
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'dob' => $request->dob,
                    'photo' => $photoPath
                ]);

                // Create User record for login (Matched to your users table schema)
                User::create([
                    'role' => 'teacher',
                    'name' => $request->name,
                    'school_name' => $school->school_name,
                    'mobile' => $request->mobile,
                    'id_number' => $formattedTeacherId,
                    'password' => Hash::make($request->password),
                ]);

                return [
                    'mobile' => $request->mobile,
                    'school_name' => $school->school_name,
                    'teacher_id' => $formattedTeacherId,
                    'teacher' => $teacher
                ];
            });

            // Send SMS notification
            $smsResponse = $this->sendTeacherSMS($school, $result['mobile'], $result['school_name'], $result['teacher_id']);

            return response()->json([
                'message' => 'Teacher created successfully. ID: ' . $result['teacher_id'],
                'teacher' => $result['teacher'],
                'sms_status' => [
                    'success' => $smsResponse['success'],
                    'message' => $smsResponse['success'] ? 'SMS Sent' : ($smsResponse['message'] ?? 'SMS Failed'),
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Create Teacher Error: ' . $e->getMessage());

            // Return 422 so the frontend SweetAlert captures the specific SQL/PHP error as a validation detail
            return response()->json([
                'errors' => [
                    'exception' => [$e->getMessage()]
                ],
                'message' => 'Failed to create teacher'
            ], 422);
        }
    }

    public function show(Request $request, $id)
    {
        $school = School::where('user_id', $request->user()->id)->first();
        $teacher = Teacher::where('school_id', $school->id)->findOrFail($id);
        return response()->json($teacher);
    }

    public function update(Request $request, $id)
    {
        $school = School::where('user_id', $request->user()->id)->first();

        try {
            $teacher = Teacher::where('school_id', $school->id)->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'designation' => 'nullable|string|max:255',
                'mobile' => 'required|string|unique:teachers,mobile,' . $teacher->id,
                'email' => 'required|email|unique:teachers,email,' . $teacher->id,
                'password' => 'nullable|string|min:6|confirmed',
                'dob' => 'nullable|date',
                'photo' => 'nullable|image|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            DB::transaction(function () use ($request, $teacher) {
                if ($request->hasFile('photo')) {
                    if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                        Storage::disk('public')->delete($teacher->photo);
                    }
                    $teacher->photo = $request->file('photo')->store('teacher_photos', 'public');
                }

                $teacher->name = $request->name;
                $teacher->designation = $request->designation;
                $teacher->mobile = $request->mobile;
                $teacher->email = $request->email;
                $teacher->dob = $request->dob;

                $updateData = [
                    'mobile' => $request->mobile,
                    'name' => $request->name
                ];

                if ($request->password) {
                    $hashedPassword = Hash::make($request->password);
                    $teacher->password = $hashedPassword;
                    $updateData['password'] = $hashedPassword;
                }

                // Sync with User Table via id_number
                User::where('id_number', $teacher->id_number)
                    ->where('role', 'teacher')
                    ->update($updateData);

                $teacher->save();
            });

            return response()->json(['message' => 'Teacher updated successfully', 'teacher' => $teacher]);
        } catch (\Exception $e) {
            Log::error('Update Teacher Error: ' . $e->getMessage());
            return response()->json([
                'errors' => [
                    'exception' => [$e->getMessage()]
                ],
                'message' => 'Failed to update teacher'
            ], 422);
        }
    }

    public function destroy(Request $request, $id)
    {
        $school = School::where('user_id', $request->user()->id)->first();

        try {
            DB::transaction(function () use ($school, $id) {
                $teacher = Teacher::where('school_id', $school->id)->findOrFail($id);

                if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                    Storage::disk('public')->delete($teacher->photo);
                }

                // Delete login user via id_number
                User::where('id_number', $teacher->id_number)
                    ->where('role', 'teacher')
                    ->delete();

                $teacher->delete();
            });

            return response()->json(['message' => 'Teacher deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Delete Teacher Error: ' . $e->getMessage());
            return response()->json([
                'errors' => [
                    'exception' => [$e->getMessage()]
                ],
                'message' => 'Failed to delete teacher'
            ], 422);
        }
    }

    private function sendTeacherSMS($school, $mobile, $schoolName, $teacherId)
    {
        $message = "Welcome! Your registration at $schoolName is confirmed. Teacher ID: $teacherId. You can now login to your portal. Regards, $schoolName.";
        return $this->smsService->sendSms($school, $mobile, $message);
    }
}
