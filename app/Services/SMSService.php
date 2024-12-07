<?php

namespace App\Services;

use Infobip\Configuration;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsTextualMessage;
use Infobip\Model\SmsDestination;
use Illuminate\Support\Facades\Log;

class SMSService
{
    private $client;
    private $configuration;

    public function __construct()
    {
        try {
            // Explicitly read environment variables with more robust retrieval
            $baseUrl = config('services.infobip.base_url');
            $apiKey = config('services.infobip.api_key');
            $senderId = config('services.infobip.sender_id', 'SCSHSAdmin');

            // Extensive logging for debugging
            Log::info('Infobip Configuration Detailed', [
                'base_url' => $baseUrl,
                'api_key' => $apiKey ? 'Present (masked)' : 'Missing',
                'sender_id' => $senderId
            ]);

            // Validate base URL
            if (empty($baseUrl)) {
                throw new \Exception('Infobip Base URL is not set in configuration');
            }

            // Validate API key
            if (empty($apiKey)) {
                throw new \Exception('Infobip API Key is not set in configuration');
            }

            // Ensure base URL has https://
            if (!preg_match('/^https?:\/\//', $baseUrl)) {
                $baseUrl = 'https://' . $baseUrl;
            }

            // Create configuration with base URL and API key
            $this->configuration = new Configuration($baseUrl, $apiKey);

            // Create SMS API client
            $this->client = new SmsApi($this->configuration);

        } catch (\Exception $e) {
            // Comprehensive error logging
            Log::error('SMS Service Initialization Failure', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            // Explicitly set client to null
            $this->client = null;

            // Rethrow the exception
            throw $e;
        }
    }

    public function sendBulkSMS(array $phoneNumbers, string $messageText)
    {
        // Validate client initialization
        if ($this->client === null) {
            Log::error('SMS Service Client Not Initialized', [
                'error' => 'Client is null',
                'phone_numbers' => $phoneNumbers
            ]);
            return [
                'success' => false,
                'error' => 'SMS Service not properly configured'
            ];
        }

        // Early validation of input
        if (empty($phoneNumbers)) {
            Log::warning('No phone numbers provided for SMS sending');
            return [
                'success' => false,
                'error' => 'No phone numbers provided'
            ];
        }

        // Get sender ID from configuration
        $senderId = config('services.infobip.sender_id', 'SCSHSAdmin');

        try {
            // Comprehensive destination preparation with detailed logging
            $destinations = [];
            foreach ($phoneNumbers as $phone) {
                // Ensure phone is a string and not empty
                $cleanPhone = trim((string)$phone);

                // Log each phone number processing
                Log::info('Processing Phone Number', [
                    'original' => $phone,
                    'cleaned' => $cleanPhone
                ]);

                // Additional validation
                if (empty($cleanPhone)) {
                    Log::warning('Skipping empty phone number', ['phone' => $phone]);
                    continue;
                }

                // More strict phone number validation
                if (!preg_match('/^\+639\d{9}$/', $cleanPhone)) {
                    Log::warning('Invalid phone number format', ['phone' => $cleanPhone]);
                    continue;
                }

                // Create destination with explicit to parameter
                try {
                    $destination = new SmsDestination([
                        'to' => $cleanPhone
                    ]);
                    $destinations[] = $destination;

                    // Log successful destination creation
                    Log::info('Destination Created', [
                        'phone' => $cleanPhone
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create destination', [
                        'phone' => $cleanPhone,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Check if destinations are empty after filtering
            if (empty($destinations)) {
                Log::error('No valid destinations after filtering', [
                    'original_numbers' => $phoneNumbers
                ]);
                throw new \Exception('No valid phone numbers found after filtering');
            }

            // Detailed logging of destinations
            Log::info('Prepared Destinations', [
                'destination_count' => count($destinations),
                'destination_numbers' => array_map(function($dest) {
                    return $dest->getTo();
                }, $destinations)
            ]);

            // Create SMS message with more explicit configuration
            $message = new SmsTextualMessage();
            $message->setFrom($senderId);
            $message->setDestinations($destinations);
            $message->setText($messageText);

            // Prepare advanced request with additional error handling
            $request = new SmsAdvancedTextualRequest();
            $request->setMessages([$message]);

            // Add additional configuration options
            $request->setValidityPeriod(24); // Optional: set validity period in hours

            // Detailed logging before sending
            Log::info('SMS Sending Attempt', [
                'sender' => $senderId,
                'destination_count' => count($destinations),
                'destinations' => array_map(function($dest) { return $dest->getTo(); }, $destinations),
                'message_length' => strlen($messageText)
            ]);

            // Send SMS with comprehensive error handling
            try {
                $response = $this->client->sendSmsMessage($request);

                // Log successful response
                Log::info('SMS Sending Successful', [
                    'response' => method_exists($response, 'toArray')
                        ? $response->toArray()
                        : (string)$response
                ]);

                return [
                    'success' => true,
                    'response' => $response
                ];

            } catch (\Exception $sendException) {
                // Capture and log any sending-specific errors
                Log::error('SMS Sending Exception', [
                    'error_message' => $sendException->getMessage(),
                    'error_code' => $sendException->getCode(),
                    'response_body' => method_exists($sendException, 'getResponseBody')
                        ? $sendException->getResponseBody()
                        : 'No response body'
                ]);

                return [
                    'success' => false,
                    'error' => $sendException->getMessage(),
                    'error_details' => method_exists($sendException, 'getResponseBody')
                        ? $sendException->getResponseBody()
                        : null
                ];
            }

        } catch (\Exception $e) {
            // Catch-all error handling
            Log::error('SMS Preparation Failed', [
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_trace' => $e->getTraceAsString(),
                'destinations' => $phoneNumbers
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_details' => null
            ];
        }
    }

    private function prepareDestinations(array $phoneNumbers): array
    {
        $destinations = [];
        $validPhoneNumbers = [];

        foreach ($phoneNumbers as $phone) {
            $formattedPhone = $this->formatPhoneNumber($phone);

            if ($formattedPhone) {
                try {
                    $destination = new SmsDestination($formattedPhone);
                    $destinations[] = $destination;
                    $validPhoneNumbers[] = $formattedPhone;
                } catch (\Exception $e) {
                    Log::warning('Failed to create destination', [
                        'number' => $phone,
                        'formatted' => $formattedPhone,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        Log::info('Prepared Destinations', [
            'original_count' => count($phoneNumbers),
            'valid_count' => count($destinations),
            'valid_numbers' => $validPhoneNumbers
        ]);

        return $destinations;
    }

    public function formatPhoneNumber($phone): ?string
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/\D/', '', $phone);

        // Comprehensive phone number formatting for Philippine numbers
        $formats = [
            // 09 format
            ['/^09(\d{9})$/', '+639$1'],
            // 9 format
            ['/^9(\d{9})$/', '+639$1'],
            // 639 format
            ['/^639(\d{9})$/', '+639$1'],
            // 63 format
            ['/^63(\d{10})$/', '+63$1'],
            // +63 format
            ['/^\+63(\d{10})$/', '+63$1'],
        ];

        foreach ($formats as $format) {
            if (preg_match($format[0], $cleaned)) {
                $formatted = preg_replace($format[0], $format[1], $cleaned);

                // Additional validation for Philippine number length
                if (strlen($formatted) === 13 && strpos($formatted, '+639') === 0) {
                    return $formatted;
                }
            }
        }

        Log::warning('Unrecognized or Invalid Phone Number', [
            'original' => $phone,
            'cleaned' => $cleaned
        ]);

        return null;
    }
}
