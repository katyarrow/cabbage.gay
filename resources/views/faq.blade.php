<x-main-layout>
    <h1 class="text-3xl font-semibold tracking-wider text-center mb-5">Frequently Asked Questions</h1>
    <ul class="max-w-lg mx-auto">
        <li class="my-10">
            <h2 class="text-xl font-semibold tracking-wider text-left">1. What is {{ config('app.name') }}?</h2>
            <p>
                {{ config('app.name') }} is an end to end encrypted meeting poll application / scheduler.
                It provides more security (see question 2) than an application that is not end to end encrypted as
                the server never sees your data (see question 3).
            </p>
        </li>
        <li class="my-10">
            <h2 class="text-xl font-semibold tracking-wider text-left">2. What are the limits of end to end encryption?</h2>
            <p>
                End to end encryption provides an extra layer of security by encrypting data from the point of sending it to
                the point of viewing it. This protects the data during transit and storage on the websites server.
                What it <em>does not</em> do is stop malicious programs and individuals on your computer from viewing the
                data that has been decrypted. This means that theoretically your browser or other programs on your computer
                can see the decrypted data you are viewing.<br/>
                Furthermore, if someone has access to the unique meeting url then they will be able to view the data regardless.
            </p>
        </li>
        <li class="my-10">
            <h2 class="text-xl font-semibold tracking-wider text-left">3. Is any data stored unencrypted on the server?</h2>
            <p>
                Yes! While as much data as possible is encrypted, some data is required to be stored unencrypted for the
                functioning of the site. This data includes the following:
                <ul class="list-disc list-inside">
                    <li class="my-2">Session cookies.</li>
                    <li class="my-2">Destroy dates of meetings.</li>
                    <li class="my-2">Meeting identifiers used in the URL.</li>
                    <li class="my-2">Unique attendee identifiers (an impossible to guess random string used to identify encrypted response data to update or delete it).</li>
                    <li class="my-2">The public part of an asymetric cryptographic keypair unique to each meeting.</li>
                </ul>
                Beyond this, an attacker who has managed to access the server could trivially perform an analysis of
                a meeting and determine how many responses it has had. Now this data would be pretty useless without being able
                to decrypt either the meeting or responses data; however it is something you may want to be aware of.
                <br>
                <br>
                All other data is encrypted.
            </p>
        </li>
        <li class="my-10">
            <h2 class="text-xl font-semibold tracking-wider text-left">4. Is this site secure?</h2>
            <p>
                While all possible precautions have been taken to ensure the security of the site
                there is always a possibility that vulnerabilities exist. If you want to check out the source code
                to suggest changes of check for vulnerabilities please feel free to visit
                <a href="https://github.com/katyarrow/cabbage.gay" class="underline text-green-600">the github</a>.
            </p>
        </li>
        <li class="my-10">
            <h2 class="text-xl font-semibold tracking-wider text-left">5. Is this a paid service?</h2>
            <p>
                No, this site will not be paywalled or make money off you in any other way
                (though a donation link may be set up in future to cover maintenance and upkeep).
            </p>
        </li>
        <li class="my-10">
            <h2 class="text-xl font-semibold tracking-wider text-left">6. Will you sell my data?</h2>
            <p>
                Absolutely not.
            </p>
        </li>
        <li class="my-10">
            <h2 class="text-xl font-semibold tracking-wider text-left">7. How is the server hosted?</h2>
            <p>
                We use <a href="https://1984.hosting/" class="underline text-green-600">1984.hosting</a>.
            </p>
        </li>
    </ul>
</x-main-layout>