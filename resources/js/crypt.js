import sodium from 'libsodium-wrappers';

class Crypt {

    key = null;
    privateKey = null;
    publicKey = null;

    static generateFromUrl() {
        let key = location.href.split('#')[1];
        if(key == undefined) {
            throw new Error('No key in url.');
        }
        let crypt = new Crypt();
        crypt.key = key;
        return crypt;
    }

    /**
     * Appends the key in plaintext to the url after the # symbol.
     * @param {String} url
     * @returns {String} the url plus the key in the form "url#key".
     */
    appendKeyToUrl(url) {
        return url + '#' + this.key;
    }

    async awaitReady() {
        await sodium.ready;
    }

    /**
     * Concatenate two Uint8Arrays.
     * @param {Uint8Array} arr1
     * @param {Uint8Array} arr2
     * @returns
     */
    concatUint8Arrays(arr1, arr2) {
        let mergedArray = new Uint8Array(arr1.length + arr2.length);
        mergedArray.set(arr1);
        mergedArray.set(arr2, arr1.length);
        return mergedArray;
    }

    /**
     * Generates a symmetric key and assigns to key attributes.
     */
    newSymmetricKey() {
        this.key = sodium.to_hex(sodium.crypto_secretbox_keygen());
    }

    /**
     * Generates a new asymmetric keypair for privateKey and publicKey attributes.
     */
    newAsymmetricKeypair() {
        let keypair = sodium.crypto_sign_keypair();
        this.privateKey = sodium.to_hex(keypair.privateKey);
        this.publicKey = sodium.to_hex(keypair.publicKey);
    }

    /**
     * Generates a fresh set of symmetric and assymetric keys.
     */
    freshKeys() {
        this.newSymmetricKey();
        this.newAsymmetricKeypair();
    }

    /**
     * Encrypts the string with prepended the nonce.
     * @param {String} message
     * @returns {String} a hex string of the encrypted message.
     */
    encrypt(message) {
        let nonce = sodium.randombytes_buf(sodium.crypto_secretbox_NONCEBYTES);
        return sodium.to_hex(this.concatUint8Arrays(nonce, sodium.crypto_secretbox_easy(message, nonce, sodium.from_hex(this.key))));
    }

    /**
     * Decrypts the ciphertext with prepended nonce from the encrypt() method.
     * @param {String} nonce_and_ciphertext
     * @returns {String} the plaintext of the given hex ciphertext.
     */
    decrypt(nonce_and_ciphertext) {
        nonce_and_ciphertext = sodium.from_hex(nonce_and_ciphertext);
        if (nonce_and_ciphertext.length < sodium.crypto_secretbox_NONCEBYTES + sodium.crypto_secretbox_MACBYTES) {
            throw "Short message";
        }
        let nonce = nonce_and_ciphertext.slice(0, sodium.crypto_secretbox_NONCEBYTES);
        let ciphertext = nonce_and_ciphertext.slice(sodium.crypto_secretbox_NONCEBYTES);
        return sodium.to_string(sodium.crypto_secretbox_open_easy(ciphertext, nonce, sodium.from_hex(this.key)));
    }

    /**
     * Get the signature of a piece of text as a hex string using the privateKey.
     * @param {String} text
     * @returns {String} a hex string signature.
     */
    getSignature(text) {
        return sodium.to_hex(sodium.crypto_sign_detached(text, sodium.from_hex(this.privateKey)));
    }

    /**
     * Extracts fields in given order from formObject and generates a signature using the getSignature() method.
     * @param {Array} orderedFields
     * @param {Object} formObject
     * @returns {String} signature
     */
    getSignatureFromFormObject(orderedFields, formObject) {
        let text = '';
        orderedFields.forEach(fieldName => {
            if(!formObject[fieldName]) return;
            text += formObject[fieldName];
        });
        return this.getSignature(text);
    }
}

window.Crypt = Crypt;