<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use App\Models\AdmissionStudent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use function Illuminate\Log\log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'password'   => 'required'
        ]);

        // Clean input for cPanel/Linux Case Sensitivity
        $identifier = strtolower(trim($request->identifier));

        // 1. First, find the user by ID Number (Primary for everyone)
        // 2. Or find by Email/Mobile ONLY if the user is an admin
        $user = User::where(function ($query) use ($identifier) {
            $query->where('id_number', $identifier)
                ->orWhere(function ($adminQuery) use ($identifier) {
                    $adminQuery->where('role', 'admin')
                        ->where(function ($sub) use ($identifier) {
                            $sub->whereRaw('LOWER(email) = ?', [$identifier])
                                ->orWhere('mobile', $identifier);
                        });
                });
        })->first();

        // Log::info($user);

        // Check credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Log::info($user);

        // Approval Guards
        if ($user->role === 'school') {
            $school = School::where('user_id', $user->id)->first();
            if (!$school || $school->approval_status !== 'approved') {
                return response()->json(['message' => 'Account pending admin approval.'], 403);
            }
        }

        if ($user->role === 'student') {
            $student = AdmissionStudent::where('mobile', $user->mobile)->first();
            if (!$student || $student->status !== 'approved') {
                return response()->json(['message' => 'Admission pending approval.'], 403);
            }
        }

        /* =====================================================
             SANCTUM AUTHENTICATION (Token-based)
        ====================================================== */
        
        // Create Sanctum token for authentication
        $token = $user->createToken('auth_token')->plainTextToken;

        // Establish session for web middleware compatibility
        Auth::login($user);
        
        // Regenerate session to prevent session fixation
        request()->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
            'redirect' => match ($user->role) {
                'admin'   => '/admin/dashboard',
                'school'  => '/school/dashboard',
                'teacher' => '/teacher/dashboard',
                'student' => '/student/dashboard',
                default   => '/404',
            },
            'token' => $token
        ])->withCookie(cookie('auth_token', $token, 60 * 24 * 30)); // 30 days
    }

    public function logout(Request $request)
    {
        
        // Delete Sanctum tokens
        $request->user()->tokens()->delete();
        
        // Logout from session as well
        Auth::guard('web')->logout();
        
        // Invalidate session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return response()->json([
            'message'  => 'Logged out successfully',
            'redirect' => '/'
        ]);
    }
}