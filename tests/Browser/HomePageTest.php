<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomePageTest extends DuskTestCase
{
    public function test_homepage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');

            // General check for base inputs.
            $browser->assertTitle('Home - cabbage.gay');
            $browser->assertSee('Create a Meeting');
            $browser->assertInputPresent('name');
            $browser->assertInputPresent('start_date');
            $browser->assertInputPresent('end_date');
            $browser->assertInputPresent('specific_dates');
            $browser->assertInputPresent('entire_period');
            $browser->assertInputPresent('start_time');
            $browser->assertInputPresent('end_time');
            $browser->assertInputPresent('destroy_at');
            $browser->assertInputPresent('timezone');

            // Checking dates and times are correctly set on page load.
            $browser->assertInputValue('start_date', now()->format('Y-m-d'));
            $browser->assertInputValue('end_date', now()->addDays(6)->format('Y-m-d'));
            $browser->assertSelected('start_time', '09:00');
            $browser->assertSelected('end_time', '17:00');

            // Checking that specific date checkbox works and displays correct inputs.
            $browser->assertDontSee(now()->addDays(1)->format('D jS M'));
            $browser->assertDontSee(now()->addDays(2)->format('D jS M'));
            $browser->assertInputMissing(now()->addDays(1)->format('Y-m-d'));
            $browser->assertInputMissing(now()->addDays(2)->format('Y-m-d'));

            $browser->check('specific_dates');
            $this->typeDateField($browser, '#start_date', now()->addDays(1));
            $this->typeDateField($browser, '#end_date', now()->addDays(3));

            $browser->assertSee(now()->addDays(1)->format('D jS M'));
            $browser->assertSee(now()->addDays(2)->format('D jS M'));
            $browser->assertInputPresent(now()->addDays(1)->format('Y-m-d'));
            $browser->assertInputPresent(now()->addDays(2)->format('Y-m-d'));

            // Checking that destroy date changes.
            $this->typeDateField($browser, '#start_date', now()->addDays(5));
            $browser->assertInputValue('destroy_at', now()->addDays(6)->format('Y-m-d'));
        });
    }

    public function test_homepage_submit(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');

            $browser->type('name', 'test123');
            $this->typeDateField($browser, '#start_date', now()->addDays(1));
            $this->typeDateField($browser, '#end_date', now()->addDays(7));
            $browser->select('start_time', '06:00');
            $browser->select('end_time', '12:00');
            $this->typeDateField($browser, '#destroy_at', now()->addDays(8));
            $browser->select('timezone', 'Europe/London');

            $browser->press('button[type="submit"]');
            $browser->waitFor('.pow-captcha');
            $browser->assertSee('Verifying');

            $browser->waitForReload();

            $url = $browser->driver->getCurrentURL();
            $meetingId = explode('#', explode(config('app.url').'/meeting/', $url)[1])[0];
            dump($meetingId);
            $browser->assertRouteIs('meeting.show', ['meeting' => $meetingId]);
            $this->assertDatabaseHas('meetings', [
                'identifier' => $meetingId,
            ]);
        });
    }
}
