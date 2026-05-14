<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    /**
     * Change password for authenticated user (Admin, School, Student, Teacher, Guardian)
     */
    public function changePassword(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'current_password' => ['required', 'string'],
                'new_password' => [
                    'required',
                    'string',
                    'confirmed',
                    'min:8',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ], [
                'current_password.required' => 'Current password is required.',
                'new_password.required' => 'New password is required.',
                'new_password.confirmed' => 'Passwords do not match.',
                'new_password.min' => 'Password must be at least 8 characters.',
                'new_password' => 'Password must contain uppercase, lowercase, numbers and symbols.',
            ]);

            $user = auth()->user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.',
                ], 422);
            }

            // Check if new password is same as current
            if (Hash::check($request->new_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'New password must be different from current password.',
                ], 422);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully!',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
