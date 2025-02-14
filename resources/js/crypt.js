import sodium from 'libsodium-wrappers';

/**
 * Concatenate two Uint8Arrays.
 * @param {Uint8Array} arr1
 * @param {Uint8Array} arr2
 * @returns
 */
function concatUint8Arrays(arr1, arr2) {
    let mergedArray = new Uint8Array(arr1.length + arr2.length);
    mergedArray.set(arr1);
    mergedArray.set(arr2, arr1.length);
    return mergedArray;
}

/**
 * @returns {string} a symmetric key as a hex string.
 */
function newSymmetricKey() {
    return sodium.to_hex(sodium.crypto_secretbox_keygen());
}

/**
 * @returns {Object} an object in the form {privateKey: hex string, publicKey: hex string}.
 */
function newAsymmetricKeypair() {
    return sodium.crypto_sign_keypair();
}

/**
 * Encrypts the string with prepended the nonce.
 * @param {String} message
 * @param {String} key must be a hex string (for example generated from newSymmetricKey()).
 * @returns {String} a hex string of the encrypted message.
 */
function encrypt(message, key) {
    let nonce = sodium.randombytes_buf(sodium.crypto_secretbox_NONCEBYTES);
    return sodium.to_hex(concatUint8Arrays(nonce, sodium.crypto_secretbox_easy(message, nonce, sodium.from_hex(key))));
}

/**
 * Decrypts the ciphertext with prepended nonce from the encrypt() function.
 * @param {String} nonce_and_ciphertext
 * @param {String} key must be a hex string (for example generated from newSymmetricKey()).
 * @returns {String} the plaintext of the given hex ciphertext.
 */
function decrypt(nonce_and_ciphertext, key) {
    nonce_and_ciphertext = sodium.from_hex(nonce_and_ciphertext);
    if (nonce_and_ciphertext.length < sodium.crypto_secretbox_NONCEBYTES + sodium.crypto_secretbox_MACBYTES) {
        throw "Short message";
    }
    let nonce = nonce_and_ciphertext.slice(0, sodium.crypto_secretbox_NONCEBYTES),
        ciphertext = nonce_and_ciphertext.slice(sodium.crypto_secretbox_NONCEBYTES);
    return sodium.to_string(sodium.crypto_secretbox_open_easy(ciphertext, nonce, sodium.from_hex(key)));
}

/**
 * Get the signature of a piece of text as a hex string using the privateKey.
 * @param {String} text
 * @param {String} privateKey must be a hex string (for example generated from newSymmetricKey()).
 * @returns {String} a hex string signature.
 */
function getSignature(text, privateKey) {
    return sodium.to_hex(sodium.crypto_sign_detached(text, privateKey));
}