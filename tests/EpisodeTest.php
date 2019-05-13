<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-25
 * Time: 16:02
 */

namespace App\Tests;


use App\Controller\EpisodeController;
use App\Controller\TvShowController;
use App\Entity\Episode;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class EpisodeTest extends WebTestCase
{

    /** @var EpisodeController */
    private $episode;

    /** @var TvShowController */
    private $tvShow;

    /** @var EntityManager */
    private $entityManager;

    private $container2;

    /**
     * EpisodeTest constructor.
     */
    public function setUp()
    {
        $client = self::createClient();
        $this->container2 = $client->getContainer();

        $this->episode = new EpisodeController();
        $this->tvShow = new TvShowController();

        $user = new User();
        $user->setEmail('test@email.com');
        $user->setName('test');
        $user->setLastname('aaa');
        $user->setPassword('test');
        $user->setCoGoogle(false);

        $mockUser = $this->createMock(TokenInterface::class);
        $mockUser->method('getUser')->willReturn($user);
        $this->container2->get('security.token_storage')->setToken($mockUser);

        $this->episode->setContainer($this->container2);
        $this->tvShow->setContainer($this->container2);

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

    public function testCheckWithoutFollowTvShow()
    {
        $request = new Request();
        $request->request->add(['episode' => ['idSerie' => 192, 'idEpisode' => 530411]]);

        $json = $this->episode->checkEpisode($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
    }

    public function testCheckWithFollowTvShow()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $request2 = new Request();
        $request2->request->add(['episode' => ['idSerie' => 192, 'idEpisode' => 530411]]);

        $json = $this->episode->checkEpisode($request2);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
    }

    public function testCheckExistantEpisodeWithFollowTvShow()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $request2 = new Request();
        $request2->request->add(['episode' => ['idSerie' => 192, 'idEpisode' => 530411]]);

        $json = $this->episode->checkEpisode($request2);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);

        $this->assertEquals(1, count($this->entityManager->getRepository(Episode::class)->findAll()));

        $this->addNewUser();

        $this->tvShow->followTvShow($request);

        $json = $this->episode->checkEpisode($request2);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);

        $this->assertEquals(1, count($this->entityManager->getRepository(Episode::class)->findAll()));
    }

    public function testUnCheckEpisodeWithFollowTvShow()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $request2 = new Request();
        $request2->request->add(['episode' => ['idSerie' => 192, 'idEpisode' => 530411]]);

        $json = $this->episode->checkEpisode($request2);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);

        $this->assertEquals(1, count($this->entityManager->getRepository(Episode::class)->findAll()));

        $this->episode->unCheckEpisode($request2);
        $this->assertEquals(0, count($this->entityManager->getRepository(Episode::class)->findAll()));
    }

    public function testUnCheckEpisodeWhitoutSerieFollow()
    {
        $request2 = new Request();
        $request2->request->add(['episode' => ['idSerie' => 192, 'idEpisode' => 530411]]);

        $json = $this->episode->unCheckEpisode($request2);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
    }

    public function testUnCheckEpisodeNotExist()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $request2 = new Request();
        $request2->request->add(['episode' => ['idSerie' => 192, 'idEpisode' => 530431]]);

        $json = $this->episode->unCheckEpisode($request2);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
    }

    public function testUnCheckEpisodeWithMany()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl', 'test' => true]]);

        $this->tvShow->followTvShow($request);

        $request2 = new Request();
        $request2->request->add(['episode' => ['idSerie' => 192, 'idEpisode' => 530411]]);

        $json = $this->episode->checkEpisode($request2);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);

        $this->assertEquals(1, count($this->entityManager->getRepository(Episode::class)->findAll()));

        $this->addNewUser();

        $this->tvShow->followTvShow($request);
        $this->episode->checkEpisode($request2);

        $this->episode->unCheckEpisode($request2);
        $this->assertEquals(1, count($this->entityManager->getRepository(Episode::class)->findAll()));
    }

}