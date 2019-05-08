<?php


namespace App\Tests;

use App\Controller\GenreController;
use App\Entity\Genre;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class GenreTest extends WebTestCase
{
    /** @var EntityManager */
    private $entityManager;

    /** @var GenreController */
    private $genreController;

    /** @var Container */
    private $container2;

    protected function setUp()
    {
        $client = self::createClient();
        $this->container2 = $client->getContainer();

        $this->genreController = new GenreController();
        $this->genreController->setContainer($this->container2);

        $user = new User();
        $user->setEmail('test@email.com');
        $user->setName('test');
        $user->setLastname('aaa');
        $user->setPassword('test');
        $user->setCoGoogle(false);

        $mockUser = $this->createMock(TokenInterface::class);
        $mockUser->method('getUser')->willReturn($user);
        $this->container2->get('security.token_storage')->setToken($mockUser);


        // Run the schema update tool using our entity metadata
        $this->entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->updateSchema($metadatas);
    }

    public function addNewUser()
    {
        $user = new User();
        $user->setEmail('tesat@email.com');
        $user->setName('tesat');
        $user->setLastname('aaaa');
        $user->setPassword('tesat');
        $user->setCoGoogle(false);

        $mockUser = $this->createMock(TokenInterface::class);
        $mockUser->method('getUser')->willReturn($user);
        $this->container2->get('security.token_storage')->setToken($mockUser);


        $this->genreController->setContainer($this->container2);
    }

    public function testAddGenre()
    {
        $request = new Request();
        $request->request->add(['genre' => 'claude']);

        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));
    }

    public function testAddGenreDoublon()
    {
        $request = new Request();
        $request->request->add(['genre' => 'claude']);

        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));

        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));
    }

    public function testAdd2Genre()
    {
        $request = new Request();
        $request->request->add(['genre' => 'claude']);

        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));

        $request2 = new Request();
        $request2->request->add(['genre' => 'bernard']);

        $json = $this->genreController->addGenre($request2);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(2, count($this->entityManager->getRepository(Genre::class)->findAll()));
    }

    public function testAddGenreFor2User()
    {
        $request = new Request();
        $request->request->add(['genre' => 'claude']);

        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));

        $this->addNewUser();
        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));
    }

    public function testRemoveGenre()
    {
        $request = new Request();
        $request->request->add(['genre' => 'claude']);

        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));

        $json = $this->genreController->removeGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(0, count($this->entityManager->getRepository(Genre::class)->findAll()));
    }

    public function testRemoveGenreNotDelete()
    {
        $request = new Request();
        $request->request->add(['genre' => 'claude']);

        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));

        $this->addNewUser();
        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));

        $json = $this->genreController->removeGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));
    }

    public function testRemoveGenreNotExiste()
    {
        $request = new Request();
        $request->request->add(['genre' => 'claude']);

        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));

        $request = new Request();
        $request->request->add(['genre' => 'bernard']);

        $json = $this->genreController->removeGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
        $this->assertEquals("Le genre n'existe pas", $res['content']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));
    }

    public function testRemoveGenreNotExisteForUser()
    {
        $request = new Request();
        $request->request->add(['genre' => 'claude']);

        $json = $this->genreController->addGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));

        $this->addNewUser();

        $request = new Request();
        $request->request->add(['genre' => 'claude']);

        $json = $this->genreController->removeGenre($request);

        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
        $this->assertEquals("Vous n'avez pas le genre", $res['content']);
        $this->assertEquals(1, count($this->entityManager->getRepository(Genre::class)->findAll()));
    }
}