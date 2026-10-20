<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CreateRotaPageTest extends DuskTestCase
{
    public function test_create_rota_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/rota');

            $browser->assertHasHeaderAndFooter();

            // General check for base inputs.
            $browser->assertTitle('Rota - cabbage.gay');
            $browser->assertSee('Create a Rota');
            $browser->assertInputPresent('name');
            $browser->assertInputPresent('destroy_at');

            // Checking that destroy date correct default value.
            $browser->assertInputValue('destroy_at', now()->addDays(6)->format('Y-m-d'));
        });
    }

    public function test_create_rota_page_submit(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/rota');

            $browser->type('name', 'test123');
            $browser->typeDateField('#destroy_at', now()->addDays(8));

            $browser->press('button[type="submit"]');
            $browser->waitFor('.pow-captcha');
            $browser->assertSee('Verifying');

            $browser->waitForReload();

            $url = $browser->driver->getCurrentURL();
            $rotaId = explode('#', explode(config('app.url').'/rota/', $url)[1])[0];
            $browser->assertRouteIs('rota.show', ['rota' => $rotaId]);
            $this->assertDatabaseHas('rotas', [
                'identifier' => $rotaId,
            ]);
        });
    }
}
