<?php

namespace Tests\Browser;

use Carbon\Carbon;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OptionalPeriodMeetingTest extends DuskTestCase
{
    public function test_it_shows_optional_period_meeting(): void
    {
        $this->browse(function (Browser $browser) {
            $start = now()->endOfMonth()->addMonth()->subDays(2);
            $end = now()->endOfMonth()->addMonth()->addDays(8);
            $browser->createMeeting([
                'name' => 'test meeting 123',
                'entire_period' => false,
                'start_time' => '10:00',
                'end_time' => '20:00',
                'start_date' => $start,
                'end_date' => $end,
                'timezone' => 'Europe/London',
            ]);
            $browser->assertSee('test meeting 123');
            $browser->assertButtonEnabled('@add-availability-btn');
            $browser->assertButtonEnabled('@share-btn');
            $browser->assertSee('10 am - 8 pm (Europe/London)');
            $browser->assertSelectHasOptions('select#display_type', [
                'simple',
                'proportion',
                'gradient',
                'numbers',
                'gradient_numbers',
            ]);
            $browser->assertSelected('select#display_type', 'proportion');

            $browser->assertSee($start->format('F') . ' - ' . $end->format('F'));

            $browser->assertSeeIn('thead th:nth-child(2)', $start->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(2)', $start->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(3)', $start->clone()->addDays(1)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(3)', $start->clone()->addDays(1)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(4)', $start->clone()->addDays(2)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(4)', $start->clone()->addDays(2)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(5)', $start->clone()->addDays(3)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(5)', $start->clone()->addDays(3)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(6)', $start->clone()->addDays(4)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(6)', $start->clone()->addDays(4)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(7)', $start->clone()->addDays(5)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(7)', $start->clone()->addDays(5)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(8)', $start->clone()->addDays(6)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(8)', $start->clone()->addDays(6)->format('j'), true);
            $this->assertSeesTimes($browser, '10:00', '20:00');
            $browser->assertSee('Responses (0)');
            $browser->assertSee('Page 1 / 2');

            $browser->assertMissing('@prev-page-btn');
            $browser->assertButtonEnabled('@next-page-btn');

            $browser->click('@next-page-btn');

            $browser->assertSeeIn('thead th:nth-child(2)', $start->clone()->addDays(7)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(2)', $start->clone()->addDays(7)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(3)', $start->clone()->addDays(8)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(3)', $start->clone()->addDays(8)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(4)', $start->clone()->addDays(9)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(4)', $start->clone()->addDays(9)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(5)', $start->clone()->addDays(10)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(5)', $start->clone()->addDays(10)->format('j'), true);
            $this->assertSeesTimes($browser, '10:00', '20:00');

            $browser->assertButtonEnabled('@prev-page-btn');
            $browser->assertMissing('@next-page-btn');
            $browser->assertSee('Page 2 / 2');
            $browser->assertSee($end->format('F'));

            $browser->click('@prev-page-btn');

            $browser->assertSee($start->format('F') . ' - ' . $end->format('F'));
            $browser->assertSeeIn('thead th:nth-child(2)', $start->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(2)', $start->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(3)', $start->clone()->addDays(1)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(3)', $start->clone()->addDays(1)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(4)', $start->clone()->addDays(2)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(4)', $start->clone()->addDays(2)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(5)', $start->clone()->addDays(3)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(5)', $start->clone()->addDays(3)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(6)', $start->clone()->addDays(4)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(6)', $start->clone()->addDays(4)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(7)', $start->clone()->addDays(5)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(7)', $start->clone()->addDays(5)->format('j'), true);
            $browser->assertSeeIn('thead th:nth-child(8)', $start->clone()->addDays(6)->format('D'), true);
            $browser->assertSeeIn('thead th:nth-child(8)', $start->clone()->addDays(6)->format('j'), true);
            $this->assertSeesTimes($browser, '10:00', '20:00');

            $browser->assertButtonEnabled('@next-page-btn');
            $browser->assertMissing('@prev-page-btn');
            $browser->assertSee('Page 1 / 2');
            $browser->assertSee($start->format('F') . ' - ' . $end->format('F'));
        });
    }

    private function assertSeesTimes(Browser $browser, string $start, string $end) {
        $current = Carbon::createFromFormat('H:i', $start);
        $end = Carbon::createFromFormat('H:i', $end);
        while($current < $end) {
            $browser->assertSee($current->format('g a'));
            $current->addHour();
        }
    }
}
