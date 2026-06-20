<?php
// symfony php bin/phpunit tests/Controller/ConferenceControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Factory\CommentFactory;
use App\Factory\ConferenceFactory;

use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ConferenceControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request("GET", "/");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("h2", "Give your feedback");
    }

    public function testConferencePage(): void
    {
        $client = static::createClient();

        $amsterdam = ConferenceFactory::createOne(["city" => "Amsterdam", "year" => "2019", "isInternational" => true]);
        ConferenceFactory::createOne(["city" => "Paris", "year" => "2020", "isInternational" => false]);
        CommentFactory::createOne(["conference" => $amsterdam]);

        $crawler = $client->request("GET", "/");

        $this->assertCount(2, $crawler->filter("h4"));

        $client->clickLink("View");

        $this->assertPageTitleContains("Amsterdam");
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("h2", "Amsterdam 2019");
        $this->assertSelectorExists('div:contains("There are 1 comments")');
    }
}
