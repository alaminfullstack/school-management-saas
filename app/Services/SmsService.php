<?php

namespace App\Services;

use App\Models\SmsPackagePurchase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SmsService
{
    /**
     * Send SMS with automatic balance deduction and expiry tracking
     * * @param object $school The school model/object
     * @param string $mobile Target phone number
     * @param string $message The SMS content
     * @return array
     */
    public function sendSms($school, $mobile, $message)
    {
        try {
            // 1. Setup Time and Cost
            $now = Carbon::now('Asia/Dhaka');
            $charCount = mb_strlen($message, 'UTF-8');
            $smsCost = (int) ceil($charCount / 70); // Unicode logic

            // 2. Determine which balance to use
            // First: Try to find a valid specific purchase record (FIFO)
            $activePurchase = SmsPackagePurchase::where('school_id', $school->id)
                ->where('status', 'approved')
                ->where('expiry_date', '>=', $now)
                ->where('available_sms', '>=', $smsCost)
                ->orderBy('expiry_date', 'asc')
                ->first();

            // Second: If no specific package, check the main school table balance
            if (!$activePurchase) {
                // Refresh school data to get latest balance from DB
                $currentSchoolBalance = DB::table('schools')->where('id', $school->id)->value('sms_balance');

                if ($currentSchoolBalance < $smsCost) {
                    return [
                        'success' => false,
                        'message' => 'SMS balance insufficient. Current Balance: ' . ($currentSchoolBalance ?? 0)
                    ];
                }
            }

            // 3. Format Phone (880 format)
            $phone = preg_replace('/[^0-9]/', '', $mobile);
            if (str_starts_with($phone, '0')) {
                $phone = '880' . substr($phone, 1);
            }

            // 4. Send to Gateway
            $baseUrl = rtrim(env('SMS_BASE_URL'), '/');
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout(15)
                ->post("{$baseUrl}/api/SmsSending/SMS", [
                    "UserName"        => (string) env('SMS_USERNAME'),
                    "Apikey"          => (string) env('SMS_API_KEY'),
                    "MobileNumber"    => (string) $phone,
                    "CampaignId"      => "null",
                    "SenderName"      => (string) env('SMS_SENDER_ID'),
                    "TransactionType" => "T",
                    "Message"         => (string) $message
                ]);

            $result = $response->json();

            // 5. Post-check & Dual-Table Deduction
            if ($response->successful() && isset($result['statusCode']) && $result['statusCode'] == 200) {

                DB::transaction(function () use ($school, $activePurchase, $smsCost) {
                    if ($activePurchase) {
                        // Case A: Deduct from the specific purchase record
                        $activePurchase->decrement('available_sms', $smsCost);
                        $activePurchase->increment('used_sms', $smsCost);
                    }

                    // Always sync/deduct the main school balance for quick dashboard viewing
                    // This covers both Case A (Syncing) and Case B (Direct deduction from school table)
                    DB::table('schools')->where('id', $school->id)->decrement('sms_balance', $smsCost);
                });

                return [
                    'success' => true,
                    'trxnId'  => $result['trxnId'] ?? null,
                    'cost'    => $smsCost
                ];
            }

            return [
                'success' => false,
                'message' => $result['responseResult'] ?? $result['message'] ?? 'Gateway Rejection'
            ];
        } catch (\Exception $e) {
            Log::error("SmsService Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'SMS Gateway Connection Failed'
            ];
        }
    }
}
