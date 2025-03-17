<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Auth\SocialiteProviders\Medialogic;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use SocialiteProviders\Azure\Provider;

class MdlAzureProvider extends Provider
{
    public const IDENTIFIER = 'MDL-AZURE';

    public function getGroupsByToken($token)
    {
        $final_result = [];
        try {
            $response = $this->getHttpClient()->get($this->graphUrl . '/memberOf', [
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                RequestOptions::PROXY => $this->getConfig('proxy'),
            ]);


            $parsed_response = json_decode((string)$response->getBody(), true);


            $final_result = array_merge($final_result, $this->organizeMemberOfResponse($parsed_response));

            if (isset($parsed_response['@odata.nextLink'])) {
                do {
                    $response = $this->getHttpClient()->get($parsed_response['@odata.nextLink'], [
                        RequestOptions::HEADERS => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer ' . $token,
                        ],
                        RequestOptions::PROXY => $this->getConfig('proxy'),
                    ]);
                    $parsed_response = json_decode((string)$response->getBody(), true);

                    $final_result = array_merge($final_result, $this->organizeMemberOfResponse($parsed_response));
                } while (isset($parsed_response['@odata.nextLink']));
            }
        } catch (\Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
        }

        return $final_result;
    }

    private function organizeMemberOfResponse(array $decodeResponse): array
    {

        if (isset($decodeResponse['@odata.context']) && $decodeResponse['@odata.context'] == 'https://graph.microsoft.com/v1.0/$metadata#directoryObjects' && isset($decodeResponse['value'])) {
            return collect($decodeResponse['value'])->pluck('displayName', 'id')->reject(function ($value, $key) {
                return empty($value) || empty($key);
            })->toArray();
        }
        return [];
    }

    public function userWithGroups()
    {
        $user = $this->user();
        $user->user['groups'] = $this->getGroupsByToken($user->token);
        //$user->user['profileImage'] = $this->getProfileImage();
        return $user;
    }

    public function getProfileImage($token = null)
    {
        $result = null;
        try {
            $token = $token ?? $this->user()->token;
            $response = $this->getHttpClient()->get($this->graphUrl . '/photo/$value', [
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                RequestOptions::PROXY => $this->getConfig('proxy'),
                RequestOptions::STREAM => true
            ]);

            $result = [
                'type' => Arr::first($response->getHeader('Content-Type')),
                'content' => base64_encode($response->getBody()->getContents())
            ];
        } catch (\Exception $e) {
            Log::channel('auth')->error($e->getMessage());
            Log::channel('auth')->error($e->getTraceAsString());
        }
        return $result;
    }
}