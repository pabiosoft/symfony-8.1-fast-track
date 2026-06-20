<?php
// symfony php bin/phpunit tests/Controller/ConferenceControllerTest.php
namespace App\Tests\Controller;

use App\Factory\CommentFactory;
use App\Factory\ConferenceFactory;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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

    public function testCommentSubmission(): void
    {
        $client = static::createClient();

        $berlin = ConferenceFactory::createOne(["city" => "Berlin", "year" => "2021", "isInternational" => false]);
        CommentFactory::createOne(["conference" => $berlin]);

        $client->request("GET", "/conference/berlin-2021");
        $client->submitForm("Submit", [
            "comment[author]" => "Fabien",
            "comment[text]" => "Some feedback from an automated functional test",
            "comment[email]" => ($email = "me@pab.ed"),
            "comment[photo]" => dirname(__DIR__, 2) . "/public/images/under-construction.gif",
        ]);
        $this->assertResponseRedirects();

        // simulate comment validation
        $comment = self::getContainer()->get(CommentRepository::class)->findOneByEmail($email);
        $comment->setState("published");
        self::getContainer()->get(EntityManagerInterface::class)->flush();

        $client->followRedirect();
        $this->assertSelectorExists('div:contains("There are 2 comments")');
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
