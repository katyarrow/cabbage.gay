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
}
