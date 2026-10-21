<x-main-layout
    title="Instructions for Use"
    description="Learn more about how to use cabbage.gay."
>
    <div class="max-w-lg mx-auto">
        <h1 class="text-3xl font-semibold tracking-wider text-center mb-5">Instructions for Use</h1>
        <h2 class="text-xl font-semibold tracking-wider text-left">Meetings</h2>
        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Creating a meeting</h3>
        <p>
            On the homepage navigate to create a meeting. On the create meeting page there are the following inputs:
        </p>
        <ul>
            <li>
                <u>Meeting Name:</u> <br>
                The name of the meeting. This should be a descriptor or a reference to what the meeting will be about.
            </li>
            <li>
                <u>Date range for the meeting:</u> <br>
                Choose two dates between which you want to poll meeting times for.
                If you want more granular control of which days are displayed on the poll you can check
                "Select Specific Dates" which will bring up a list of dates between your chosen dates that
                you can toggle on and off.
            </li>
            <li>
                <u>Time range for the meeting:</u> <br>
                Choose two times between 12am and 11pm between which you want to poll meeting times for.
                If the meeting is an all day affair you can check "Require availability for entire period?".
            </li>
            <li>
                <u>Destroy Date:</u> <br>
                The date the poll will be destroyed at, defaults to the end date of the poll.
            </li>
            <li>
                <u>Timezone:</u> <br>
                The timezone will be automatically selected for the timezone you are currently in however if you
                are making a poll for a different timezone it may be worth changing this to the appropriate value.1
            </li>
        </ul>

        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Adding availability</h3>
        <p>
            After creating your meeting you can add your availability to the poll using the
            "add availability" button on the top right.
            You can click and drag across the date and time grid to set yourself to available on those days.
            To set yourself to maybe available, click on an available square and drag your cursor/finger
            across the grid again.
            To set yourself as unavailable repeat the above process on a "maybe available" square.
            Finally, add your name at the bottom and click "Finish".
        </p>

        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Viewing availabilities</h3>
        <p>
            When you load the poll, the availabilities of people who have filled out the poll will be
            shown to you. You can select different viewing modes using the drop down box below the date/time grid.
            If there are many days on the poll you may need to click next page.
            To see who has vote what on specific date/timeslots you can click on a slot in the grid and
            peoples names on the right hand side or below the poll will change accordingly
            (green with a tick for available, yellow with a question mark for maybe, gray with a cross for no).
        </p>

        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Deleting availabilities</h3>
        <p>
            To delete a persons response you can go to the list of people to the right of the poll on desktops and
            and below the poll on mobile and click the red cross next to their name.
        </p>

        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Symbol Mode</h3>
        <p>
            Below the "add availability" button on the top right of the poll page you can engage "symbol mode"
            with the checkbox. This will display the poll with symbols as well as colors.
        </p>

        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Sharing</h3>
        <p>
            You can share the poll by either copying the link to the poll from your browser bar or
            clicking the share buttong which will do the same thing. You can then send the link to people.
        </p>

        <h2 class="text-xl font-semibold tracking-wider text-left mt-10">Rotas</h2>

        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Creating a rota</h3>
        <p>
            On the homepage navigate to create a meeting. On the create meeting page there are the following inputs:
        </p>
        <ul>
            <li>
                <u>Meeting Name:</u> <br>
                The name of the meeting. This should be a descriptor or a reference to what the meeting will be about.
            </li>
            <li>
                <u>Destroy Date:</u> <br>
                The date the poll will be destroyed at, defaults to the end date of the poll.
            </li>
        </ul>

        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Editing roles and timeslots</h3>
        <p>
            On the top left of the poll you will find a small edit button. If you click it you will enter
            header editing mode. Here you can add new roles using the plus button on the right of the rota,
            add new timeslots using the plus button at the bottom of the rota, edit role and timeslot by clicking
            on them, move the timeslots and roles around by using the arrows, and remove the roles and timeslots
            by using the trash can icons.
            Once you are done, click the save icon where the edit icon was previously.
        </p>

        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Adding and removing people</h3>
        <p>
            To add someone to a timeslot, click the plus icon in the timeslot you want to add them to.
            This will bring up a box with a name field, from here you can enter their name manually or
            select a name from previously entered names. Once done click "Add person". <br>
            To remove a person, click the trash can icon next to their name on the rota.
        </p>

        <h3 class="font-medium mt-3 text-xl tracking-wider text-left">Sharing</h3>
        <p>
            You can share the rota by either copying the link to the rota from your browser bar or
            clicking the share buttong which will do the same thing. You can then send the link to people.
        </p>
    </div>
</x-main-layout>