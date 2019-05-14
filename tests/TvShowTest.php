<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-17
 * Time: 14:43
 */

namespace App\Tests;


use App\Controller\TvShowController;
use App\Entity\TvShow;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TvShowTest extends WebTestCase
{
    /** @var TvShowController */
    private $tvShow;

    /** @var EntityManager */
    private $entityManager;

    private $container2;

    public function setUp()
    {
        $client = self::createClient();
        $this->tvShow = new TvShowController();

        $user = new User();
        $user->setEmail('test@email.com');
        $user->setName('test');
        $user->setLastname('aaa');
        $user->setPassword('test');
        $user->setCoGoogle(false);

        $mockUser = $this->createMock(TokenInterface::class);
        $mockUser->method('getUser')->willReturn($user);
        $this->container2 = $client->getContainer();
        $this->container2->get('security.token_storage')->setToken($mockUser);


        $this->tvShow->setContainer($this->container2);

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


        $this->tvShow->setContainer($this->container2);
    }

    public function testAddSerieNotExistInBDD()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->assertEquals(0, count($this->entityManager->getRepository(TvShow::class)->findAll()));

        $this->tvShow->followTvShow($request);

        $this->assertEquals(1, count($this->entityManager->getRepository(TvShow::class)->findAll()));
    }

    public function testAddSerieExistInBDD()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $this->assertEquals(1, count($this->entityManager->getRepository(TvShow::class)->findAll()));

        $this->addNewUser();
        $this->tvShow->followTvShow($request);

        $this->assertEquals(1, count($this->entityManager->getRepository(TvShow::class)->findAll()));
    }

    public function testAddNewSerieInBDD()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $this->assertEquals(1, count($this->entityManager->getRepository(TvShow::class)->findAll()));

        $request->request->add(['serie' => ['id' => 150, 'name' => 'shameless', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $this->assertEquals(2, count($this->entityManager->getRepository(TvShow::class)->findAll()));
    }

    public function testRemoveSerieInBDD()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $this->assertEquals(1, count($this->entityManager->getRepository(TvShow::class)->findAll()));

        $this->tvShow->unfollowTvShow($request);

        $this->assertEquals(0, count($this->entityManager->getRepository(TvShow::class)->findAll()));
    }

    public function testRemoveSerieInBDDWithManyUser()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $this->assertEquals(1, count($this->entityManager->getRepository(TvShow::class)->findAll()));

        $this->addNewUser();
        $this->tvShow->followTvShow($request);

        $this->assertEquals(1, count($this->entityManager->getRepository(TvShow::class)->findAll()));

        $this->tvShow->unfollowTvShow($request);

        $this->assertEquals(1, count($this->entityManager->getRepository(TvShow::class)->findAll()));
    }

}