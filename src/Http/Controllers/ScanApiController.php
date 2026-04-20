<?php

namespace MobicardApi\ScanApi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use MobicardApi\ScanApi\Services\ScanApiService;

class ScanApiController extends Controller
{
    protected $scanApiService;

    public function __construct(ScanApiService $scanApiService)
    {
        $this->scanApiService = $scanApiService;
    }

    /**
     * Show the card scan interface
     */
    public function index()
    {
        $result = $this->scanApiService->requestAccessToken();

        if ($result['success']) {
            return view('scanapi::scan', [
                'mobicard_transaction_access_token' => $result['data']['mobicard_transaction_access_token'],
                'mobicard_token_id' => $result['data']['mobicard_token_id'],
                'mobicard_scan_card_url' => $result['data']['mobicard_scan_card_url'],
                'mobicard_txn_reference' => $result['data']['mobicard_txn_reference'],
                'debug_mode' => config('scanapi.debug', false)
            ]);
        }

        return view('scanapi::error', ['error' => $result['error']]);
    }

    /**
     * Broker endpoint for card scan
     */
    public function scanCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobicard_transaction_access_token' => 'required|string',
            'mobicard_token_id' => 'required|string',
            'mobicard_scan_card_url' => 'required|url',
            'image_data' => 'required|string',
            'file_type' => 'sometimes|string',
            'file_name' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        $result = $this->scanApiService->processCardScan(
            $request->image_data,
            $request->mobicard_transaction_access_token,
            $request->mobicard_token_id,
            $request->mobicard_scan_card_url,
            $request->file_type,
            $request->file_name
        );

        $statusCode = $result['status_code'] ?? 200;
        return response()->json($result, (int) $statusCode);
    }

    /**
     * Broker endpoint for card upload
     */
    public function uploadCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobicard_transaction_access_token' => 'required|string',
            'mobicard_token_id' => 'required|string',
            'mobicard_scan_card_url' => 'required|url',
            'mobicard_scan_card_photo' => 'required|file|mimes:jpg,jpeg,png,pdf,gif,bmp,webp,tiff|max:262144'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ], 422);
        }

        $result = $this->scanApiService->processCardUpload(
            $request->file('mobicard_scan_card_photo'),
            $request->mobicard_transaction_access_token,
            $request->mobicard_token_id,
            $request->mobicard_scan_card_url
        );

        $statusCode = $result['status_code'] ?? 200;
        return response()->json($result, (int) $statusCode);
    }
}
