<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Extensions;
use Exception;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Foundation\Application;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Facades\Agent;

class CustomDatabaseSessionHandler extends DatabaseSessionHandler
{
    public function write($sessionId, $data):bool
    {
        $payload = $this->getDefaultPayload($data);

        $payload = array_merge($payload, [
            'client' => $this->getBrowserAttribute(). ' '.$this->getOSAttribute(),
            'robot' => $this->getIsRobotAttribute(),
        ]);

        if (!$this->exists) {
            $this->read($sessionId);
        }

        if ($this->exists) {
            $this->performUpdate($sessionId, $payload);
        } else {
            $this->performInsert($sessionId, $payload);
        }

        return $this->exists = true;
    }

    public function getBrowserAttribute(): ?string
    {
        try {
            $browser = Agent::browser($this->userAgent());
            $browser_version = Agent::version($browser);

            return "$browser $browser_version";
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    public function getOSAttribute(): ?string
    {
        try {
            $platform = Agent::platform($this->userAgent());
            $platform_version = Agent::version($platform);
            return "$platform $platform_version";
        } catch(Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    public function getIsRobotAttribute(): Application|array|string|Translator|\Illuminate\Contracts\Foundation\Application|null
    {
        try {
            $isRobot = Agent::isRobot($this->userAgent());

            return $isRobot?trans('common.yes'): trans('common.no');
        } catch(Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }
}