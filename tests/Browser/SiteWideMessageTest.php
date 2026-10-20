<?php

namespace Tests\Browser;

use App\Models\SiteWideMessage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SiteWideMessageTest extends DuskTestCase
{
    public function test_displays_appropriate_site_messages(): void
    {
        $this->browse(function (Browser $browser) {
            $message = new SiteWideMessage;
            $message->message = 'Message should show.';
            $message->level = 'warning';
            $message->display_until = now()->addHours(1);
            $message->save();

            $message = new SiteWideMessage;
            $message->message = 'Message should not show.';
            $message->level = 'info';
            $message->display_until = now()->subHour(1);
            $message->save();

            $browser->visit('/');

            $browser->assertSee('Message should show.');
            $browser->assertDontSee('Message should not show.');
        });
    }
}
