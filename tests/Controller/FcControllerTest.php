<?php

namespace App\Test\Controller;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FcControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PlayerRepository $repository;
    private string $path = '/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Player::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Football Clubs');

        // Use the $crawler to perform additional assertions e.g.
        self::assertSame('Club', $crawler->filter('h1')->first()->html());
    }
}
