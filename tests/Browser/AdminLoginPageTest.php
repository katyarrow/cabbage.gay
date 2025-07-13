<?php

namespace Tests\Browser;

use App\Models\User;
use Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminLoginPageTest extends DuskTestCase
{
    public function test_it_loads_the_login_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin');
            $browser->assertTitle('Admin - cabbage.gay');
            $browser->assertSee('Admin Login');
            $browser->assertSee('Solve Captcha');
            $browser->assertInputPresent('username');
            $browser->assertInputPresent('password');
            $browser->assertButtonEnabled('Login');
        });
    }

    public function test_it_allows_admins_to_log_in(): void
    {
        $this->browse(function (Browser $browser) {
            $password = str()->random();
            $user = User::factory()->create([
                'password' => Hash::make($password),
            ]);
            $browser->visit('/admin');
            $browser->type('username', $user->username);
            $browser->type('password', $password);
            $browser->click('@solve-captcha-btn');
            $browser->waitFor('@captcha-completed-text');
            $browser->clickAndWaitForReload('button[type="submit"]');
            $browser->assertAuthenticatedAs($user);
            $browser->assertRouteIs('admin.index');
        });
    }

    public function test_it_does_not_allow_non_admins_to_log_in(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin');
            $browser->type('username', str()->random());
            $browser->type('password', str()->random());
            $browser->click('@solve-captcha-btn');
            $browser->waitFor('@captcha-completed-text');
            $browser->clickAndWaitForReload('button[type="submit"]');
            $browser->assertGuest();
            $browser->assertRouteIs('login');
        });
    }
}
