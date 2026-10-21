<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReleasesPageTest extends DuskTestCase
{
    public function test_releases_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/releases');
            $browser->assertHasHeaderAndFooter();
            $browser->assertTitle('Releases - cabbage.gay');
            $browser->assertSee('Releases');
        });
    }
}
