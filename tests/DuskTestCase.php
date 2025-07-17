<?php

namespace Tests;

use Carbon\Carbon;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Collection;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use Tests\Browser\Components\Footer;
use Tests\Browser\Components\Header;

abstract class DuskTestCase extends BaseTestCase
{
    use DatabaseTruncation;

    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        Browser::macro('waitForReload', function () {
            $this->script('window.duskPageIsStale = {}');

            return $this->waitUntil("return typeof window.duskPageIsStale === 'undefined';");
        });

        Browser::macro('assertHasHeader', function (): Browser {
            return $this->within(new Header, function (Browser $browser) {});
        });

        Browser::macro('assertHasFooter', function (): Browser {
            return $this->within(new Footer, function (Browser $browser) {});
        });

        Browser::macro('assertHasHeaderAndFooter', function (): Browser {
            return $this->assertHasFooter()->assertHasHeader();
        });

        Browser::macro('typeDateField', function (string $selector, Carbon $date): Browser {
            $dateString = $date->format('Y-m-d');
            $this->script([
                "document.querySelector('$selector').value = '$dateString'",
                "document.querySelector('$selector').dispatchEvent(new Event('change'))",
            ]);

            return $this;
        });

        Browser::macro('createMeeting', function (array $data = []): Browser {
            $this->visit('/');
            $this->type('name', $data['name'] ?? str()->random());
            if (array_key_exists('entire_period', $data) && $data['entire_period']) {
                $this->check('entire_period');
            }
            if (array_key_exists('start_date', $data)) {
                $this->typeDateField('#start_date', $data['start_date']);
            }
            if (array_key_exists('end_date', $data)) {
                $this->typeDateField('#end_date', $data['end_date']);
            }
            if (array_key_exists('start_time', $data)) {
                $this->select('start_time', $data['start_time']);
            }
            if (array_key_exists('end_time', $data)) {
                $this->select('end_time', $data['end_time']);
            }
            if (array_key_exists('destroy_at', $data)) {
                $this->typeDateField('#destroy_at', $data['destroy_at']);
            }
            if (array_key_exists('timezone', $data)) {
                $this->select('timezone', $data['timezone']);
            }

            $this->press('button[type="submit"]');

            $this->waitForReload();

            return $this;
        });

        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            '--unsafely-treat-insecure-origin-as-secure=http://laravel.test',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
