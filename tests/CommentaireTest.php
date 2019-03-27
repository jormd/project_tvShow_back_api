<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-27
 * Time: 16:25
 */

namespace App\Tests;


use App\Controller\CommentaireController;
use App\Controller\EpisodeController;
use App\Controller\TvShowController;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CommentaireTest extends WebTestCase
{

    /** @var CommentaireController */
    private $commentaire;

    /** @var EntityManager */
    private $entityManager;

    /** @var TvShowController */
    private $tvShow;

    /** @var EpisodeController */
    private $episode;

    protected function setUp()
    {
        $client = self::createClient();
        $container = $client->getContainer();

        $this->commentaire = new CommentaireController();

        $user = new User();
        $user->setEmail('test@email.com');
        $user->setName('test');
        $user->setLastname('aaa');
        $user->setPassword('test');
        $user->setCoGoogle(false);

        $mockUser = $this->createMock(TokenInterface::class);
        $mockUser->method('getUser')->willReturn($user);
        $container->get('security.token_storage')->setToken($mockUser);

        $this->commentaire->setContainer($container);

        $this->episode = new EpisodeController();
        $this->tvShow = new TvShowController();
        $this->episode->setContainer($container);
        $this->tvShow->setContainer($container);

        // Run the schema update tool using our entity metadata
        $this->entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->updateSchema($metadatas);
    }

    public function testAddCommentaireErrorNotEpisode()
    {
        $request = new Request();
        $request->request->add(['commentaire' => ['idEpisodeApi' => 192, 'commentaire' => 'girl']]);

        $json = $this->commentaire->addCommentaireEpisode($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
    }

    public function testAddCommntaireSuccess()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl']]);

        $this->tvShow->followTvShow($request);

        $request2 = new Request();
        $request2->request->add(['episode' => ['idSerie' => 192, 'idEpisode' => 530411]]);

        $json = $this->episode->checkEpisode($request2);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);

        $request3 = new Request();
        $request3->request->add(['commentaire' => ['idEpisodeApi' => 530411, 'commentaire' => 'girl']]);

        $json = $this->commentaire->addCommentaireEpisode($request3);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
    }

    public function testRemoveCommentaireError()
    {
        $request3 = new Request();
        $request3->request->add(['commentaire' => ['id' => 1]]);

        $json = $this->commentaire->removeCommentaireEpisode($request3);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
    }

    public function testRemoveCommentaireSuccess()
    {
        $request = new Request();
        $request->request->add(['serie' => ['id' => 192, 'name' => 'girl']]);

        $this->tvShow->followTvShow($request);

        $request2 = new Request();
        $request2->request->add(['episode' => ['idSerie' => 192, 'idEpisode' => 530411]]);

        $json = $this->episode->checkEpisode($request2);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);

        $request3 = new Request();
        $request3->request->add(['commentaire' => ['idEpisodeApi' => 530411, 'commentaire' => 'girl']]);

        $json = $this->commentaire->addCommentaireEpisode($request3);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);

        $request4 = new Request();
        $request4->request->add(['commentaire' => ['id' => 1]]);

        $json = $this->commentaire->removeCommentaireEpisode($request4);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
    }
}