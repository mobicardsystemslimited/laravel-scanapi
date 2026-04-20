<?php

namespace MobicardApi\ScanApi\Services;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ScanApiService
{
    protected $config;
    protected $client;

    public function __construct()
    {
        $this->config = config('scanapi');
        $this->client = new Client([
            'verify' => false, // Only for development, remove in production
            'timeout' => 30,
        ]);
    }

    /**
     * Generate JWT token for authentication
     */
    public function generateAuthJwt($tokenId = null, $txnReference = null)
    {
        $tokenId = $tokenId ?? abs(rand(1000000, 1000000000));
        $txnReference = $txnReference ?? abs(rand(1000000, 1000000000));

        // Create JWT Header
        $header = $this->base64UrlEncode(json_encode([
            "typ" => "JWT",
            "alg" => "HS256"
        ]));

        // Create JWT Payload
        $payload = $this->base64UrlEncode(json_encode([
            "mobicard_version" => $this->config['version'],
            "mobicard_mode" => $this->config['mode'],
            "mobicard_merchant_id" => $this->config['merchant_id'],
            "mobicard_api_key" => $this->config['api_key'],
            "mobicard_service_id" => $this->config['service_id'],
            "mobicard_service_type" => $this->config['service_type'],
            "mobicard_token_id" => (string) $tokenId,
            "mobicard_txn_reference" => (string) $txnReference,
            "mobicard_extra_data" => "scanapi_package_v1"
        ]));

        // Generate Signature
        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', $header . '.' . $payload, $this->config['secret_key'], true)
        );

        return [
            'jwt' => $header . '.' . $payload . '.' . $signature,
            'token_id' => $tokenId,
            'txn_reference' => $txnReference
        ];
    }

    /**
     * Request access token from ScanAPI
     */
    public function requestAccessToken()
    {
        try {
            $authData = $this->generateAuthJwt();

            $response = $this->client->post($this->config['base_url'] . '/card_scan', [
                'json' => [
                    'mobicard_auth_jwt' => $authData['jwt']
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if ($result && ($result['status_code'] ?? null) == "200") {
                return [
                    'success' => true,
                    'data' => $result,
                    'token_id' => $authData['token_id'],
                    'txn_reference' => $authData['txn_reference']
                ];
            }

            return [
                'success' => false,
                'error' => $result['status_message'] ?? 'Failed to initialize card scan session',
                'data' => $result
            ];
        } catch (RequestException $e) {
            Log::error('ScanAPI Error: ' . $e->getMessage());

            $errorMessage = 'Connection error';
            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody(), true);
                $errorMessage = $response['status_message'] ?? $e->getMessage();
            }

            return [
                'success' => false,
                'error' => $errorMessage
            ];
        }
    }

    /**
     * Process card scan
     */
    public function processCardScan($imageData, $accessToken, $tokenId, $scanUrl, $fileType = null, $fileName = null)
    {
        $tempFile = null;

        try {
            // Extract base64 data if needed
            if (strpos($imageData, 'base64,') !== false) {
                $imageData = explode('base64,', $imageData)[1];
            }

            // Decode image
            $decodedImage = base64_decode($imageData);
            if (!$decodedImage) {
                return ['error' => 'Invalid image data', 'status_code' => 400];
            }

            // Determine extension
            $extension = $fileType ?? 'jpg';
            if ($fileName) {
                $extension = pathinfo($fileName, PATHINFO_EXTENSION) ?: $extension;
            }

            // Create temp file
            $tempFile = tempnam(sys_get_temp_dir(), 'card_scan_');
            $tempFileWithExt = $tempFile . '.' . $extension;
            rename($tempFile, $tempFileWithExt);
            $tempFile = $tempFileWithExt;

            file_put_contents($tempFile, $decodedImage);

            // Prepare multipart data
            $multipart = [
                [
                    'name' => 'mobicard_scan_card_photo',
                    'contents' => fopen($tempFile, 'r'),
                    'filename' => $fileName ?: 'scan.' . $extension
                ],
                [
                    'name' => 'mobicard_transaction_access_token',
                    'contents' => $accessToken
                ],
                [
                    'name' => 'mobicard_token_id',
                    'contents' => $tokenId
                ]
            ];

            // Send to ScanAPI
            $response = $this->client->post($scanUrl, [
                'multipart' => $multipart
            ]);

            $apiResponse = json_decode($response->getBody(), true);
            $apiResponse['status_code'] = (string) $response->getStatusCode();

            return $apiResponse;
        } catch (RequestException $e) {
            Log::error('Scan Card Exception: ' . $e->getMessage());

            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody(), true);
                $response['status_code'] = (string) $e->getResponse()->getStatusCode();
                return $response;
            }

            return [
                'error' => 'Connection error: ' . $e->getMessage(),
                'status_code' => '500'
            ];
        } finally {
            // Clean up temp file if it exists
            if ($tempFile && file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Process card upload
     */
    public function processCardUpload($file, $accessToken, $tokenId, $scanUrl)
    {
        try {
            $multipart = [
                [
                    'name' => 'mobicard_scan_card_photo',
                    'contents' => fopen($file->getPathname(), 'r'),
                    'filename' => $file->getClientOriginalName()
                ],
                [
                    'name' => 'mobicard_transaction_access_token',
                    'contents' => $accessToken
                ],
                [
                    'name' => 'mobicard_token_id',
                    'contents' => $tokenId
                ]
            ];

            $response = $this->client->post($scanUrl, [
                'multipart' => $multipart
            ]);

            $apiResponse = json_decode($response->getBody(), true);
            $apiResponse['status_code'] = (string) $response->getStatusCode();

            return $apiResponse;
        } catch (RequestException $e) {
            Log::error('Upload Card Exception: ' . $e->getMessage());

            if ($e->hasResponse()) {
                $response = json_decode($e->getResponse()->getBody(), true);
                $response['status_code'] = (string) $e->getResponse()->getStatusCode();
                return $response;
            }

            return [
                'error' => 'Connection error: ' . $e->getMessage(),
                'status_code' => '500'
            ];
        }
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
