<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class RtmpService
{
    public function __construct()
    {
        //
    }

    /**
     * Sends an OTA service request by constructing and sending XML requests.
     *
     * @param  string  $data
     * @param $url
     * @return array
     */
    public function RtmpClient(string $data, $url): array
    {
        try {
            $client = new Client();
            $response = $client->post($url, [
                'body' => $data,
                'headers' => [
                    'Content-Type' => 'application/xml',
                ],
                RequestOptions::VERIFY => false,
                RequestOptions::TIMEOUT => 5,
            ]);
            if ($response->getStatusCode() === 200) {
                $response_body = (string) $response->getBody();
                Log::channel('RTMP')->info('Call to remote service successful. Remote service response body: '.$response_body);

                return [
                    'success' => true,
                    'response_xml' => $response_body
                ];
            } else {
                $response_body = (string) $response->getBody();
                Log::channel('RTMP')->info('Call to remote service failed. Remote service response body: '.$response_body);

                return [
                    'success' => false
                ];
            }
        } catch (Exception|GuzzleException $e) {
            Log::channel('RTMP')->error('Failed to connect to service: '.$e->getMessage());

            return [
                'success' => false
            ];
        }
    }
}
