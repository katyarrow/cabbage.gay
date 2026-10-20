<x-admin-layout>
    <h1 class="text-2xl font-bold mb-3">Admin Panel</h1>
    <div class="grid md:grid-cols-4 gap-5 my-5">
        <h2 class="text-xl font-bold col-span-full">STATISTICS</h2>
        <div class="border rounded-lg shadow p-5 flex items-center justify-between">
            <h3 class="text-lg font-semibold">Total Meetings</h3>
            <b>{{ \App\Models\Meeting::count() }}</b>
        </div>

        <hr class="col-span-full my-5">

        <h2 class="text-xl font-bold col-span-full">SITE MESSAGES</h2>
        <form action="{{ route('admin.site-messsage.store') }}" method="POST" class="border border-gray-700 p-5 rounded shadow col-span-full grid md:grid-cols-4 gap-5 my-5">
            @csrf
            <h3 class="text-lg font-semibold col-span-full">Add Site Message</h3>
            <div>
                <label for="message_value">Message</label> <br/>
                <textarea type="text" id="message_value" name="message_value" class="border rounded p-1 w-full"></textarea>
            </div>
            <div>
                <label for="message_level">Level</label> <br/>
                <select id="message_level" name="message_level" class="border rounded p-1 w-full">
                    <option value="info">Info</option>
                    <option value="warning">Warning</option>
                    <option value="danger">Danger</option>
                    <form action=""></form>
                </select>
            </div>
            <div>
                <label for="message_hours_to_display">Hours to display</label> <br/>
                <input type="number" value="1" id="message_hours_to_display" name="message_hours_to_display" class="border rounded p-1 w-full">
            </div>
            <div class="flex items-end justify-end">
                <button class="bg-green-500 rounded px-3 py-2 text-white font-semibold">Submit</button>
            </div>
        </form>

        <div class="col-span-full">
            <h3 class="text-lg font-semibold col-span-full">Currently Displayed Messages</h3>
            <p>Current server time {{ now()->toDateTimeString() }}</p>
            <ul>
                @forelse ($messagesCurrent as $message)
                    <li class="flex items-center justify-between p-2 border rounded mb-2">
                        <span><b>Message:</b> {{ $message->message }}</span>
                        <span><b>Level:</b> {{ $message->level }}</span>
                        <span><b>Display until:</b> {{ $message->display_until->toDateTimeString() }}</span>
                        <form action="{{ route('admin.site-messsage.delete', $message) }}" method="POST">
                            @csrf
                            @method('delete')
                            <button type="submit"><i class="fa fa-trash"></i></button>
                        </form>
                    </li>
                @empty
                    <li><i>No Messages</i></li>
                @endforelse
            </ul>
        </div>

        <div class="col-span-full">
            <h3 class="text-lg font-semibold col-span-full">Previously Displayed Messages</h3>
            <ul>
                @forelse ($messagesOld as $message)
                    <li class="flex items-center justify-between p-2 border rounded mb-2">
                        <span><b>Message:</b> {{ $message->message }}</span>
                        <span><b>Level:</b> {{ $message->level }}</span>
                        <span><b>Display until:</b> {{ $message->display_until->toDateTimeString() }}</span>
                    </li>
                @empty
                    <li><i>No Messages</i></li>
                @endforelse
            </ul>
        </div>

        <hr class="col-span-full my-5">

        <form action="{{ route('admin.killswitch') }}" method="POST"
            class="border border-red-500 p-5 rounded shadow col-span-full">
            @csrf
            <h2 class="text-xl mb-3 font-bold">DANGER ZONE</h2>
            <h3 class="text-lg font-semibold mb-3">Kill Switch</h3>
            <p class="mb-5">Use this panel to kill the application for all users.</p>
            <div class="flex md:items-center flex-col md:flex-row gap-5">
                <label for="password mr-3">Password:</label>
                <input type="password" id="password" name="password" class="col-span-2 border rounded p-1">
                @include('components.error', ['name' => 'password'])
                <span class="flex-1"></span>
                <button class="bg-red-500 rounded px-3 py-2 text-white font-semibold">KILL THE APPLICATION</button>
            </div>
        </form>
    </div>
</x-admin-layout>