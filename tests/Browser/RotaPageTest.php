<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RotaPageTest extends DuskTestCase
{
    public function test_it_shows_rota(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->createRota([
                'name' => 'test rota 123',
            ]);
            $browser->assertSee('test rota 123');
            $browser->assertButtonEnabled('@share-btn');
            $browser->assertButtonEnabled('@edit-headers-btn');
            $browser->assertSeeIn('table tbody tr:nth-child(1) th', '12pm-1pm');
            $browser->assertSeeIn('table tbody tr:nth-child(2) th', '1pm-2pm');
            $browser->assertSeeIn('table tbody tr:nth-child(3) th', '2pm-3pm');
            $browser->assertSeeIn('table tbody tr:nth-child(4) th', '3pm-4pm');
            $browser->assertSeeIn('table tbody tr:nth-child(5) th', '4pm-5pm');
            $browser->assertSeeIn('table thead th:nth-child(2)', 'Availability');
        });
    }
}
