# Encryption Protocol
A symmetric key that is never saved on the server is stored in the meeting URL *after* the # symbol (i.e. the part that is never sent to the server) used to access the meeting. This provides encryption and decryption capability on the client side.
An asymmetric keypair is also generated on the client side. The private key is encrypted with the symmetric key and the public key is sent in plain text to the server.

To sign a request the encrypted private key is provided by the server and decrypted by the symmetric key on the client side. The client then signs the payload of any POST requests to the server using the private key. This can then be checked by the public key on the server to ensure that the client very likely has access to the symmetric key and is not just sending bogus data.

Crucially, anything encrypted was encrypted using the symmetric key and the private key is used only to sign the data.

## What the server sees
- encrypted private key
- plaintext public key
- Encrypted meeting info