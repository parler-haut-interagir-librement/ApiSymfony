<?php

declare(strict_types=1);

namespace App\Tests\E2E\Controller;

use App\Controller\HomeAction;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Panther\PantherTestCase;

/**
 * The home page in a real browser, Chrome driven by Panther: the theme of the page is set by the Stimulus controller of
 * its header, so a `data-theme` on the root element proves that the JavaScript of the page runs, and a click on the
 * light icon switches it to dark. Run by `castor test:e2e` alone; Chrome and chromedriver must be on the PATH.
 *
 * @see https://github.com/symfony/panther
 */
#[CoversClass(HomeAction::class)]
final class HomeActionTest extends PantherTestCase
{
    public function testTheThemeSwitcherOfTheHomePageRuns(): void
    {
        $client = self::createPantherClient(['hostname' => 'localhost']);
        $client->request('GET', '/');

        self::assertSelectorTextContains('body', 'ApiSymfony');

        $client->waitForAttributeToContain('html', 'data-theme', 'light', 10);
        self::assertSelectorAttributeContains('html', 'data-theme', 'light');

        $client->getCrawler()->filter('a.theme-icon')->first()->click();
        $client->waitForAttributeToContain('html', 'data-theme', 'dark', 10);
        self::assertSelectorAttributeContains('html', 'data-theme', 'dark');
    }
}
