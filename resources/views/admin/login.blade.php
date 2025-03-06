<x-admin-layout>
    <h1 class="text-2xl font-bold mb-3">Admin Login</h1>
    <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-3">
        @csrf
        <div>
            <label for="username">Username:</label> <br>
            <input type="text" id="username" name="username" class="col-span-2 border rounded p-1">
            @include('components.error', ['name' => 'username'])
        </div>
        <div>
            <label for="password">Password:</label> <br>
            <input type="password" id="password" name="password" class="col-span-2 border rounded p-1">
            @include('components.error', ['name' => 'password'])
        </div>
        <pow-captcha
            challenge-route="{{ route('captcha.index') }}"
            :solve-captcha-button="true"
            :used-in-form="true"
        ></pow-captcha>
        @include('components.error', ['name' => 'captcha'])
        <div>
            <button type="submit" class="bg-green-600 text-white px-2 py-1 font-semibold rounded">Login</button>
        </div>
    </form>
</x-admin-layout>