<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FaqPageTest extends DuskTestCase
{
    public function test_example(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/faq')
                ->assertTitle('Frequently Asked Questions - cabbage.gay')
                ->assertSee('Frequently Asked Questions');
        });
    }
}
