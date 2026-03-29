<x-main-layout
    title="Home"
    description="An end to end encrypted meeting poll and rota app."
>
    <div class="text-center flex flex-col items-center gap-5 px-1">
        <h1 class="text-2xl font-semibold tracking-wider text-center mb-5">
            Hello!
            <br>
            What would you like to do?
        </h1>
        <div class="w-xs max-w-full flex flex-col gap-5">
            <v-button href="{{ route('meeting.create') }}">Create a Meeting Poll    </v-button>

            <v-button href="{{ route('rota.create') }}">Create a Rota</v-button>
        </div>
    </div>
</x-main-layout>