<?php
// symfony php bin/phpunit tests/EndToEnd
//
namespace App\Tests\EndToEnd;

// @todo Pousser la recherche de Panthere
// https://symfony.com/packages/panther
// https://les-tilleuls.coop/blog/panther-php-symfony
//
use Symfony\Component\Panther\PantherTestCase;

class ConferenceEndToEndTest extends PantherTestCase
{
    public function testHomepageWithRealBrowser(): void
    {
        $client = static::createPantherClient([
            "browser" => self::CHROME,
            "external_base_uri" => rtrim($_SERVER["SYMFONY_PROJECT_DEFAULT_ROUTE_URL"], "/"),
        ]);

        $client->request("GET", "/");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("h2", "Give your feedback");
    }
}
