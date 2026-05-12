<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DynamicOperation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminDynamicOperationController extends Controller
{
    private function getSettings()
    {
        // Use find(1) to keep the global config centralized
        return DynamicOperation::find(1) ?? new DynamicOperation();
    }

    /**
     * Helper to delete the row if all monitored fields are empty
     */
    private function cleanupIfEmpty($settings)
    {
        if (!$settings->exists) return null;

        $fields = [
            'brand_title',
            'brand_description',
            'promotion_text',
            'brand_logo',
            'school_dashboard_logo',
            'brand_banner',
            'school_dashboard_banners'
        ];

        foreach ($fields as $field) {
            if (!empty($settings->$field)) {
                return $settings; // Data exists, keep the row
            }
        }

        $settings->delete();
        return new DynamicOperation(); // Return empty object for UI consistency
    }

    public function index()
    {
        return response()->json($this->getSettings());
    }

    public function updateText(Request $request)
    {
        $settings = DynamicOperation::firstOrCreate(['id' => 1]);
        $allowed = ['brand_title', 'brand_description', 'promotion_text'];

        $field = $request->field;
        if (!in_array($field, $allowed)) {
            return response()->json(['message' => 'Invalid field selection'], 422);
        }

        $settings->$field = $request->value;
        $settings->save();

        $settings = $this->cleanupIfEmpty($settings);
        return response()->json($settings);
    }

    public function uploadLogo(Request $request)
    {
        $settings = DynamicOperation::firstOrCreate(['id' => 1]);
        $field = $request->field;
        $allowed = ['brand_logo', 'school_dashboard_logo', 'brand_banner'];

        if (!in_array($field, $allowed)) {
            return response()->json(['message' => 'Invalid asset field'], 422);
        }

        // Validate for premium standard (max 2MB, common image types)
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('dynamic', 'public');

            // Clean up old file to save storage
            if ($settings->$field) {
                Storage::disk('public')->delete($settings->$field);
            }

            $settings->$field = $path;
            $settings->save();
        }

        return response()->json($settings);
    }

    public function uploadSchoolBanners(Request $request)
    {
        $settings = DynamicOperation::firstOrCreate(['id' => 1]);
        $existing = $settings->school_dashboard_banners ?? [];

        // Strict Enforcement: Check limit before processing
        if (count($existing) >= 3) {
            return response()->json([
                'message' => 'Elite standard limit reached: Maximum 3 banners allowed.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (count($existing) >= 3) break;
                $existing[] = $file->store('dynamic', 'public');
            }
        }

        $settings->school_dashboard_banners = $existing;
        $settings->save();

        return response()->json($settings);
    }

    public function deleteImage(Request $request)
    {
        $settings = DynamicOperation::find(1);
        if (!$settings) return response()->json(new DynamicOperation());

        $field = $request->field;

        if ($settings->$field && is_string($settings->$field)) {
            Storage::disk('public')->delete($settings->$field);
            $settings->$field = null;
            $settings->save();
        }

        $settings = $this->cleanupIfEmpty($settings);
        return response()->json($settings);
    }

    public function deleteSchoolBanner(Request $request)
    {
        $settings = DynamicOperation::find(1);
        if (!$settings) return response()->json(new DynamicOperation());

        $index = $request->index;
        $banners = $settings->school_dashboard_banners ?? [];

        if (isset($banners[$index])) {
            Storage::disk('public')->delete($banners[$index]);
            array_splice($banners, $index, 1);

            // Re-index to maintain clean JSON array in DB
            $settings->school_dashboard_banners = array_values($banners);
            $settings->save();
        }

        $settings = $this->cleanupIfEmpty($settings);
        return response()->json($settings);
    }
}