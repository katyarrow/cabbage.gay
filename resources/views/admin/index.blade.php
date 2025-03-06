<x-admin-layout>
    <h1 class="text-2xl font-bold mb-3">Admin Panel</h1>
    <div class="grid md:grid-cols-4 gap-5 my-5">
        <form action="{{ route('admin.killswitch') }}" method="POST"
            class="border border-red-500 p-5 rounded shadow col-span-full">
            @csrf
            <h2 class="text-lg font-semibold mb-3">Kill Switch</h2>
            <p class="mb-5">Use this panel to kill the application for all users.</p>
            <div class="flex md:items-center flex-col md:flex-row gap-5">
                <label for="password mr-3">Password:</label>
                <input type="password" id="password" name="password" class="col-span-2 border rounded p-1">
                @include('components.error', ['name' => 'password'])
                <span class="flex-1"></span>
                <button class="bg-red-500 rounded px-3 py-2 text-white font-semibold">KILL THE APPLICATION</button>
            </div>
        </form>

        <div class="border rounded-lg shadow p-5 flex items-center justify-between">
            <h2 class="text-lg font-semibold">Total Meetings</h2>
            <b>{{ \App\Models\Meeting::count() }}</b>
        </div>
    </div>
</x-admin-layout>