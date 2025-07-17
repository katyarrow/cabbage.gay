<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EntirePeriodMeetingTest extends DuskTestCase
{
    public function test_it_shows_entire_period_meeting(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->createMeeting([
                'name' => 'test meeting 123',
                'entire_period' => true,
                'start_time' => '10:00',
                'end_time' => '20:00',
                'start_date' => now()->addDays(1),
                'end_date' => now()->addDays(10),
                'timezone' => 'Europe/London',
            ]);
            $browser->assertSee('test meeting 123');
            $browser->assertButtonEnabled('@add-availability-btn');
            $browser->assertButtonEnabled('@share-btn');
            $browser->assertSee('10 am - 8 pm (Europe/London)');
            $browser->assertSee(now()->addDays(1)->format('D jS M'));
            $browser->assertSee(now()->addDays(2)->format('D jS M'));
            $browser->assertSee(now()->addDays(3)->format('D jS M'));
            $browser->assertSee(now()->addDays(4)->format('D jS M'));
            $browser->assertSee(now()->addDays(5)->format('D jS M'));
            $browser->assertSee(now()->addDays(6)->format('D jS M'));
            $browser->assertSee(now()->addDays(7)->format('D jS M'));
            $browser->assertSee(now()->addDays(8)->format('D jS M'));
            $browser->assertSee(now()->addDays(9)->format('D jS M'));
            $browser->assertSee(now()->addDays(10)->format('D jS M'));
            $browser->assertSee('Responses (0)');
        });
    }

    public function test_it_submits_and_shows_availabilities(): void
    {
        $this->browse(function (Browser $browser) {
            $day1 = now()->addDays(1);
            $day1Text = 'availability_'.$day1->format('Ymd');
            $day2 = now()->addDays(2);
            $day2Text = 'availability_'.$day2->format('Ymd');
            $day3 = now()->addDays(3);
            $day3Text = 'availability_'.$day3->format('Ymd');

            $browser->createMeeting([
                'name' => 'Meeting name 312',
                'entire_period' => true,
                'start_time' => '07:00',
                'end_time' => '12:00',
                'start_date' => $day1,
                'end_date' => $day3,
                'timezone' => 'Europe/London',
            ]);
            $browser->assertSee('Meeting name 312');
            $browser->assertSee('7 am - 12 pm (Europe/London)');
            $browser->assertSee($day1->format('D jS M'));
            $browser->assertSee($day2->format('D jS M'));
            $browser->assertSee($day3->format('D jS M'));
            $browser->assertSee('Responses (0)');

            // Checking if buttons and name input are set up correctly
            $browser->click('@add-availability-btn');
            $browser->assertButtonEnabled('@cancel-adding-availability-btn');
            $browser->assertInputPresent('name');
            $browser->assertButtonDisabled('@finish-button');
            $browser->type('name', 'blah123');
            $browser->assertButtonEnabled('@finish-button');

            // Checking if inputs for availability exist
            $browser->assertSee($day1->format('D jS M'));
            $browser->assertPresent('input[name="'.$day1Text.'"][value="yes"]');
            $browser->assertPresent('input[name="'.$day1Text.'"][value="maybe"]');
            $browser->assertPresent('input[name="'.$day1Text.'"][value="no"]');

            $browser->assertSee($day2->format('D jS M'));
            $browser->assertPresent('input[name="'.$day2Text.'"][value="yes"]');
            $browser->assertPresent('input[name="'.$day2Text.'"][value="maybe"]');
            $browser->assertPresent('input[name="'.$day2Text.'"][value="no"]');

            $browser->assertSee($day3->format('D jS M'));
            $browser->assertPresent('input[name="'.$day3Text.'"][value="yes"]');
            $browser->assertPresent('input[name="'.$day3Text.'"][value="maybe"]');
            $browser->assertPresent('input[name="'.$day3Text.'"][value="no"]');

            $browser->assertRadioSelected($day1Text, 'no');
            $browser->assertRadioSelected($day2Text, 'no');
            $browser->assertRadioSelected($day3Text, 'no');

            // select values and submit
            $browser->click('input[name="'.$day1Text.'"][value="yes"] + span');
            $browser->click('input[name="'.$day2Text.'"][value="maybe"] + span');
            $browser->click('input[name="'.$day3Text.'"][value="no"] + span');

            $browser->assertRadioSelected($day1Text, 'yes');
            $browser->assertRadioSelected($day2Text, 'maybe');
            $browser->assertRadioSelected($day3Text, 'no');

            $browser->click('@finish-button');
            $browser->pause(500);

            // Check submission went correctly.
            $browser->assertSee('Responses (1)');
            $browser->assertSee('blah123');

            $browser->assertSeeIn('@'.$day1Text.'_yes', 1);
            $browser->assertSeeIn('@'.$day1Text.'_maybe', 0);
            $browser->assertSeeIn('@'.$day1Text.'_no', 0);

            $browser->assertSeeIn('@'.$day2Text.'_yes', 0);
            $browser->assertSeeIn('@'.$day2Text.'_maybe', 1);
            $browser->assertSeeIn('@'.$day2Text.'_no', 0);

            $browser->assertSeeIn('@'.$day3Text.'_yes', 0);
            $browser->assertSeeIn('@'.$day3Text.'_maybe', 0);
            $browser->assertSeeIn('@'.$day3Text.'_no', 1);

            // Check submission went correctly after page refresh.
            $browser->refresh();

            $browser->assertSee('Meeting name 312');
            $browser->assertSee('7 am - 12 pm (Europe/London)');
            $browser->assertSee($day1->format('D jS M'));
            $browser->assertSee($day2->format('D jS M'));
            $browser->assertSee($day3->format('D jS M'));
            $browser->assertSee('Responses (1)');
            $browser->assertSee('blah123');

            $browser->assertSeeIn('@'.$day1Text.'_yes', 1);
            $browser->assertSeeIn('@'.$day1Text.'_maybe', 0);
            $browser->assertSeeIn('@'.$day1Text.'_no', 0);

            $browser->assertSeeIn('@'.$day2Text.'_yes', 0);
            $browser->assertSeeIn('@'.$day2Text.'_maybe', 1);
            $browser->assertSeeIn('@'.$day2Text.'_no', 0);

            $browser->assertSeeIn('@'.$day3Text.'_yes', 0);
            $browser->assertSeeIn('@'.$day3Text.'_maybe', 0);
            $browser->assertSeeIn('@'.$day3Text.'_no', 1);

            // Make another submission
            $browser->click('@add-availability-btn');
            $browser->click('input[name="'.$day1Text.'"][value="yes"] + span');
            $browser->click('input[name="'.$day2Text.'"][value="yes"] + span');
            $browser->click('input[name="'.$day3Text.'"][value="no"] + span');
            $browser->type('name', 'test321');

            $browser->click('@finish-button');
            $browser->pause(500);

            // Check submission went correctly.
            $browser->assertSee('Responses (2)');
            $browser->assertSee('blah123');
            $browser->assertSee('test321');

            $browser->assertSeeIn('@'.$day1Text.'_yes', 2);
            $browser->assertSeeIn('@'.$day1Text.'_maybe', 0);
            $browser->assertSeeIn('@'.$day1Text.'_no', 0);

            $browser->assertSeeIn('@'.$day2Text.'_yes', 1);
            $browser->assertSeeIn('@'.$day2Text.'_maybe', 1);
            $browser->assertSeeIn('@'.$day2Text.'_no', 0);

            $browser->assertSeeIn('@'.$day3Text.'_yes', 0);
            $browser->assertSeeIn('@'.$day3Text.'_maybe', 0);
            $browser->assertSeeIn('@'.$day3Text.'_no', 2);

            // Check submission went correctly after page refresh.
            $browser->refresh();

            $browser->assertSee('Meeting name 312');
            $browser->assertSee('7 am - 12 pm (Europe/London)');
            $browser->assertSee($day1->format('D jS M'));
            $browser->assertSee($day2->format('D jS M'));
            $browser->assertSee($day3->format('D jS M'));
            $browser->assertSee('Responses (2)');
            $browser->assertSee('blah123');
            $browser->assertSee('test321');

            $browser->assertSeeIn('@'.$day1Text.'_yes', 2);
            $browser->assertSeeIn('@'.$day1Text.'_maybe', 0);
            $browser->assertSeeIn('@'.$day1Text.'_no', 0);

            $browser->assertSeeIn('@'.$day2Text.'_yes', 1);
            $browser->assertSeeIn('@'.$day2Text.'_maybe', 1);
            $browser->assertSeeIn('@'.$day2Text.'_no', 0);

            $browser->assertSeeIn('@'.$day3Text.'_yes', 0);
            $browser->assertSeeIn('@'.$day3Text.'_maybe', 0);
            $browser->assertSeeIn('@'.$day3Text.'_no', 2);

        });
    }

    public function test_it_deletes_submisions(): void
    {
        $this->browse(function (Browser $browser) {
            $day1 = now()->addDays(1);
            $day1Text = 'availability_'.$day1->format('Ymd');
            $day2 = now()->addDays(2);
            $day2Text = 'availability_'.$day2->format('Ymd');

            $browser->createMeeting([
                'name' => 'Meeting name 312',
                'entire_period' => true,
                'start_time' => '07:00',
                'end_time' => '12:00',
                'start_date' => $day1,
                'end_date' => $day2,
                'timezone' => 'Europe/London',
            ]);
            $browser->assertSee('Meeting name 312');
            $browser->assertSee('7 am - 12 pm (Europe/London)');
            $browser->assertSee($day1->format('D jS M'));
            $browser->assertSee($day2->format('D jS M'));
            $browser->assertSee('Responses (0)');

            // Checking if buttons and name input are set up correctly
            $browser->click('@add-availability-btn');
            $browser->type('name', 'blah123');

            // select values and submit
            $browser->click('input[name="'.$day1Text.'"][value="yes"] + span');
            $browser->click('input[name="'.$day2Text.'"][value="maybe"] + span');

            $browser->assertRadioSelected($day1Text, 'yes');
            $browser->assertRadioSelected($day2Text, 'maybe');

            $browser->click('@finish-button');
            $browser->pause(500);

            // Check submission went correctly.
            $browser->assertSee('Responses (1)');
            $browser->assertSee('blah123');

            // Delete response
            $browser->click('@delete_responder_blah123');

            $browser->assertSee('Are you sure you want to delete this response for "blah123"?');

            $browser->click('@delete-user-btn');

            $browser->pause(500);

            $browser->assertSee('Responses (0)');
            $browser->assertDontSee('blah123');

            $browser->refresh();

            $browser->assertSee('Responses (0)');
            $browser->assertDontSee('blah123');
        });
    }
}
