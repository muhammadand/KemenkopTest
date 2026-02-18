<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSecretKeys extends Command
{
    protected $signature = 'generate:secret-keys';
    protected $description = 'Generate SECRET_KEY and SECRET_KEY_IV (alphanumeric) and save to .env';

    public function handle()
    {
        $secretKey = $this->generateRandomString(32);
        $secretIv  = $this->generateRandomString(16);

        $this->setEnvValue('KDMP_SECRET_KEY', $secretKey);
        $this->setEnvValue('KDMP_SECRET_KEY_IV', $secretIv);

        $this->info("KDMP_SECRET_KEY={$secretKey}");
        $this->info("KDMP_SECRET_KEY_IV={$secretIv}");
    }

    protected function generateRandomString($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $result;
    }

    protected function setEnvValue($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
            file_put_contents($path, $content);
        }
    }
}
