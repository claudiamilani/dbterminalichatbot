<?php
/*
 * Copyright (c) 2023-2024. Medialogic S.p.A.
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Encryption\Encrypter;

class BidxKeyGenerateCommand extends Command
{
    use ConfirmableTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bidx:generate
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the application blind indexes salt';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void|null
     */
    public function handle()
    {
        $key = $this->generateRandomKey();

        if ($this->option('show')) {
            return $this->line('<comment>'.$key.'</comment>');
        }

        // Next, we will replace the Blind Indexes key in the environment file, so it is
        // automatically setup for this developer. This key gets generated using a
        // secure random byte generator and is later base64 encoded for storage.
        if (! $this->setKeyInEnvironmentFile($key)) {
            return;
        }

        $this->laravel['config']['app.key'] = $key;

        $this->info('Blind Indexes key set successfully.');
    }

    /**
     * Generate a random key for the blind indexes.
     *
     * @return string
     */
    protected function generateRandomKey(): string
    {
        return base64_encode(Encrypter::generateKey($this->laravel['config']['app.cipher']));
    }

    /**
     * Set the Blind Indexes key in the environment file.
     *
     * @param string $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile(string $key): bool
    {
        $currentKey = $this->laravel['config']['lft.bidx_key'];

        if (strlen($currentKey) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param string $key
     * @return void
     */
    protected function writeNewEnvironmentFileWith(string $key): void
    {
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern(),
            'BIDX_KEY='.$key,
            file_get_contents($this->laravel->environmentFilePath())
        ));
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern(): string
    {
        $escaped = preg_quote('='.$this->laravel['config']['lft.bidx_key'], '/');

        return "/^BIDX_KEY$escaped/m";
    }
}
