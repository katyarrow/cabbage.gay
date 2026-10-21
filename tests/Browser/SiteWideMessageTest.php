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
            $message1 = new SiteWideMessage;
            $message1->message = 'Message should show.';
            $message1->level = 'warning';
            $message1->display_until = now()->addHours(1);
            $message1->save();

            $message2 = new SiteWideMessage;
            $message2->message = 'Message should not show.';
            $message2->level = 'info';
            $message2->display_until = now()->subHour(1);
            $message2->save();

            try {
                $browser->visit('/');

                $browser->assertSee('Message should show.');
                $browser->assertDontSee('Message should not show.');
            } finally {
                $message1->delete();
                $message2->delete();
            }
        });
    }
}
