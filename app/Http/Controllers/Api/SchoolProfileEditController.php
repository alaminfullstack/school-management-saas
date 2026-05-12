<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolProfileEditController extends Controller
{
    /**
     * Update the school profile and the associated user account.
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Validate all editable fields
        $request->validate([
            'school_name' => 'required|string|max:255',
            'mobile'      => 'required|string|max:20',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'division'    => 'nullable|string|max:100',
            'district'    => 'nullable|string|max:100',
            'upazila'     => 'nullable|string|max:100',
            'village'     => 'nullable|string|max:255',
            'eiin_number' => 'nullable|string|max:50',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $school = School::where('user_id', $user->id)->first();

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'School record not found.'
            ], 404);
        }

        // Start Transaction to ensure data integrity across both tables
        return DB::transaction(function () use ($request, $user, $school) {

            // 2. Update Users Table (Keep consistent with core school info)
            $user->update([
                'name'        => $request->school_name,
                'school_name' => $request->school_name,
                'mobile'      => $request->mobile,
                'email'       => $request->email,
            ]);

            // 3. Prepare School Table Data (Every editable field)
            $schoolData = [
                'school_name' => $request->school_name,
                'mobile'      => $request->mobile,
                'email'       => $request->email,
                'division'    => $request->division,
                'district'    => $request->district,
                'upazila'     => $request->upazila,
                'village'     => $request->village,
                'eiin_number' => $request->eiin_number,
            ];

            // Handle Logo Upload (Delete old, store new)
            if ($request->hasFile('logo')) {
                if ($school->logo && Storage::disk('public')->exists($school->logo)) {
                    Storage::disk('public')->delete($school->logo);
                }

                $path = $request->file('logo')->store('school_logos', 'public');
                $schoolData['logo'] = $path;

                // Sync to user profile_image as well for topbar consistency
                $user->update(['profile_image' => $path]);
            }

            // 4. Update Schools Table
            $school->update($schoolData);

            // 5. Return Full Data for Frontend Persistence
            return response()->json([
                'success' => true,
                'message' => 'School profile updated successfully!',
                'data' => [
                    'school_name' => $school->school_name,
                    'mobile'      => $school->mobile,
                    'email'       => $school->email,
                    'division'    => $school->division,
                    'district'    => $school->district,
                    'upazila'     => $school->upazila,
                    'village'     => $school->village,
                    'eiin_number' => $school->eiin_number,
                    'logo_url'    => $school->logo ? asset('storage/' . $school->logo) : asset('images/default-school.png')
                ]
            ]);
        });
    }
}