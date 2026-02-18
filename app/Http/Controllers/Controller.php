<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Encrypt data using AES-256-CBC
     */
    protected function encryptKDMPData($data)
    {
        $sharedKey = env('KDMP_SECRET_KEY');
        $sharedKeyIV = env('KDMP_SECRET_KEY_IV');

        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }

        $key = hash('sha256', $sharedKey, true);
        return openssl_encrypt($data, 'AES-256-CBC', $key, 0, $sharedKeyIV);
    }

    /**
     * Decrypt data
     */
    protected function decryptKDMPData($data, $type = 'string')
    {
        $sharedKey = env('KDMP_SECRET_KEY');
        $sharedKeyIV = env('KDMP_SECRET_KEY_IV');

        $key = hash('sha256', $sharedKey, true);
        $decrypted = openssl_decrypt($data, 'AES-256-CBC', $key, 0, $sharedKeyIV);

        return $type === 'json' ? json_decode($decrypted, true) : $decrypted;
    }
}
