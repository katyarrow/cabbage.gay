<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FaqPageTest extends DuskTestCase
{
    public function test_faq_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/faq');
            $browser->assertHasHeaderAndFooter();
            $browser->assertTitle('Frequently Asked Questions - cabbage.gay');
            $browser->assertSee('Frequently Asked Questions');
        });
    }
}
