<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InstructionsPageTest extends DuskTestCase
{
    public function test_faq_page_loads(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/instructions');
            $browser->assertHasHeaderAndFooter();
            $browser->assertTitle('Instructions for Use - cabbage.gay');
            $browser->assertSee('Instructions for Use');
        });
    }
}
