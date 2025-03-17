<?php

namespace App\Aws;

use Aws\Credentials\CredentialProvider;
use Aws\SecretsManager\SecretsManagerClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AwsSecretsManager
{
    protected $client;
    protected $configVariables;
    protected $cache;
    protected $cacheExpiry;
    protected $cacheStore;
    protected $debug;
    protected array $enabledEnvironments;
    protected bool $keyRotation;
    protected string $filterSecretsEnabled = '';
    protected string $listTagName = '';
    protected string $listTagValue = '';
    protected $retrieveSecretsList;
    protected $secretsList;

    public function __construct()
    {
        $this->retrieveSecretsList = config('aws-secrets-manager.retrieve_secrets_list', false);
        $this->filterSecretsEnabled = config('aws-secrets-manager.filter_secrets_enabled', false);
        $this->listTagName = config('aws-secrets-manager.tag_name');
        $this->listTagValue = config('aws-secrets-manager.tag_value');

        $this->secretsList = self::getSecretsList();

        $this->configVariables = self::getVariablesConfig();

        $this->cache = config('aws-secrets-manager.cache_enabled', true);

        $this->cacheExpiry = config('aws-secrets-manager.cache_expiry', 0);

        $this->cacheStore = config('aws-secrets-manager.cache_store', 'file');

        $this->enabledEnvironments = self::getEnabledEnvironments();

        $this->debug = config('aws-secrets-manager.debug', false);

        $this->keyRotation = config('aws-secrets-manager.key_rotation');
    }

    public static function getSecretsList(): array
    {
        return array_filter(explode(',', config('aws-secrets-manager.secrets_list')));
    }

    public static function getEnabledEnvironments(): array
    {
        return array_filter(explode(',', config('aws-secrets-manager.enabled_environments')));
    }

    public static function getVariablesConfig()
    {
        if(is_array($config = config('aws-secrets-manager.variables_config'))){
            return $config;
        }else{
            return collect(array_filter(explode(',', config('aws-secrets-manager.variables_config'))))
                ->mapWithKeys(function ($pair) {
                    [$configKey,$envKey] = explode(':', $pair);

                    return [$configKey => $envKey];
                })
                ->toArray();
        }
    }

    public function loadSecrets()
    {
        // Load vars from datastore to env
        if ($this->debug) {
            $start = microtime(true);
        }

        // Only run this if the evironment is enabled in the config
        if (in_array(config('app.env'), $this->enabledEnvironments)) {
            if (!$this->checkCache()) {
                // Cache has expired need to refresh the cache from Datastore
                $this->getVariables();
            }

            // Process variables in config that need updating
            $this->updateConfigs();
        }else{
            return;
        }

        if ($this->debug) {
            $time_elapsed_secs = microtime(true) - $start;
            Log::channel('aws-sm')->debug('Datastore secret request time: ' . $time_elapsed_secs);
        }
    }

    protected function checkCache()
    {
        if ($this->keyRotation) {
            $cachedNextRotationDate = Cache::store($this->cacheStore)->get('AWSSecretsNextRotationDate');
            if (
                blank($cachedNextRotationDate) ||
                $cachedNextRotationDate < Carbon::now()
            ) {
                return false;
            }
        }

        foreach ($this->configVariables as $configPath => $variable) {
            $val = Cache::store($this->cacheStore)->get($variable);

            if (!is_null($val)) {
                putenv("$variable=$val");
            } else {
                return false;
            }
        }

        return true;
    }

    protected function getVariables()
    {
        try {
            $provider = CredentialProvider::assumeRoleWithWebIdentityCredentialProvider();
            $this->client = new SecretsManagerClient([
                'version' => '2017-10-17',
                'region' => config('aws-secrets-manager.region'),
                'provider' => $provider
            ]);
            if($this->retrieveSecretsList){
                $filters = ($this->filterSecretsEnabled) ? [
                    'Filters' => [
                        [
                            'Key' => 'tag-key',
                            'Values' => [$this->listTagName],
                        ],
                        [
                            'Key' => 'tag-value',
                            'Values' => [$this->listTagValue],
                        ],
                    ],
                    'MaxResults' => 100,
                ] : [];
                $secrets = $this->client->listSecrets($filters);
            }else{
                $secrets['SecretList'] = $this->secretsList;
            }
        } catch (\Exception $e) {
            Log::channel('aws-sm')->error($e->getMessage());

            return;
        }

        if ($this->keyRotation) {
            $nextRotationDateToCache = null;
        }

        foreach ($secrets['SecretList'] as $secret) {
            if (isset($secret['ARN']) || !$this->retrieveSecretsList) {
                $result = $this->client->getSecretValue([
                    'SecretId' => is_array($secret) ? $secret['ARN'] : $secret,
                ]);

                $secretValues = json_decode($result['SecretString'], true);

                if (is_array($secretValues) && count($secretValues) > 0) {
                    if ($this->keyRotation) {
                        $nextRotationDate = Carbon::instance($secret['NextRotationDate']);
                        if ($nextRotationDate < $nextRotationDateToCache) {
                            $nextRotationDateToCache = $nextRotationDate;
                        }
                    }
                    foreach ($secretValues as $key => $value) {
                        putenv("$key=$value");
                        $this->storeToCache($key, $value);
                    }
                }
            }
        }

        if ($this->keyRotation) {
            $this->storeToCache('AWSSecretsNextRotationDate', $nextRotationDateToCache);
        }
    }

    protected function updateConfigs()
    {
        foreach ($this->configVariables as $configPath => $variable) {
            config([$configPath => env($variable)]);
        }
    }

    protected function storeToCache($name, $val)
    {
        if ($this->cache) {
            Cache::store($this->cacheStore)->put($name, $val, $this->cacheExpiry * 60);
        }
    }
}