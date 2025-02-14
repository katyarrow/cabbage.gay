<?php

namespace App\Http\Services;

use Illuminate\Http\Request;

class CryptService
{
    public const EXPECTED_ENCRYPTED_PRIVATE_KEY_LENGTH = "";
    public const EXPECTED_ENCRYPTED_PUBLIC_KEY_LENGTH = "";
    public const MAX_ENCRYPTED_DATE_LENGTH = "";
    public const MAX_ENCRYPTED_TIME_LENGTH = "";
    public const MAX_ENCRYPTED_255_LENGTH = "";

    /**
     * Verifies that the given $signature is verified against the concatenated $requestData using the given $publicKey.
     * @param array $requestData array of strings which are concatenated in the order given.
     * @param string $signature a hex representation of the signature for the concatenated $requestData.
     * @param string $publicKey a hex representation of the relevant public key.
     * @return bool
     */
    public function verifySignatureOfRequest(array $requestData, string $signature, string $publicKey) : bool {
        $publicKey = sodium_hex2bin($publicKey);
        $signature = sodium_hex2bin($signature);
        $data = implode($requestData);
        return sodium_crypto_sign_verify_detached($signature, $data, $publicKey);
    }

    /**
     * Uses verifySignatureOfRequest() to verify the given data and aborts with 400 error if invalid.
     * @param array $requestData array of strings which are concatenated in the order given.
     * @param string $signature a hex representation of the signature for the concatenated $requestData.
     * @param string $publicKey a hex representation of the relevant public key.
     * @return bool
     */
    public function abortIfInvalidRequest(array $requestData, string $signature, string $publicKey): bool {
        if(!$this->verifySignatureOfRequest($requestData, $signature, $publicKey)) {
            abort(400, "Invalid request signature for meeting.");
        }
        return true;
    }
}
