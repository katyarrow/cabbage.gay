<?php

namespace App\Http\Services;

class CryptService
{
    public const ENCRYPTED_PRIVATE_KEY_LENGTH = 336;

    public const UNENCRYPTED_PUBLIC_KEY_LENGTH = 64;

    public const MAX_MEETING_LENGTH = 10000;

    public const MAX_MEETING_ATTENDEE_LENGTH = 75000;

    /**
     * Verifies that the given $signature is verified against the concatenated $requestData using the given $publicKey.
     *
     * @param  array  $requestData  array of strings which are concatenated in the order given.
     * @param  string  $signature  a hex representation of the signature for the concatenated $requestData.
     * @param  string  $publicKey  a hex representation of the relevant public key.
     * @return bool returns false if signature or public key is invalid.
     */
    public function verifySignatureOfRequest(array $requestData, string $signature, string $publicKey): bool
    {
        $data = implode($requestData);
        try {
            return sodium_crypto_sign_verify_detached(sodium_hex2bin($signature), $data, sodium_hex2bin($publicKey));
        } catch (\SodiumException $e) {
            return false;
        }
    }

    /**
     * Uses verifySignatureOfRequest() to verify the given data and aborts with 400 error if invalid.
     *
     * @param  array  $requestData  array of strings which are concatenated in the order given.
     * @param  string  $signature  a hex representation of the signature for the concatenated $requestData.
     * @param  string  $publicKey  a hex representation of the relevant public key.
     */
    public function abortIfInvalidRequest(array $requestData, string $signature, string $publicKey): bool
    {
        if (! $this->verifySignatureOfRequest($requestData, $signature, $publicKey)) {
            abort(400, 'Invalid request signature for meeting.');
        }

        return true;
    }
}
