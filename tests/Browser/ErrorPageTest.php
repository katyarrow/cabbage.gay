<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ErrorPageTest extends DuskTestCase
{
    public function test_it_serves_a_404_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/' . str()->random())
                ->assertTitle('Not Found')
                ->assertSee('404')
                ->assertSee('Not Found', true);
        });
    }
}
