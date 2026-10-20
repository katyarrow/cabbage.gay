<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomepageTest extends DuskTestCase
{
    public function test_homepage_load(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/');

            $browser->assertHasHeaderAndFooter();

            // General check for base inputs.
            $browser->assertTitle('Home - cabbage.gay');
            $browser->assertSee('What would you like to do?');
            $browser->assertSeeIn('a[href="'.route('meeting.create').'"]', 'CREATE A MEETING POLL');
            $browser->assertSeeIn('a[href="'.route('rota.create').'"]', 'CREATE A ROTA (BETA)');
        });
    }
}
