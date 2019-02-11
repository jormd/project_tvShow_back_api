<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-10
 * Time: 17:56
 */

namespace App\Tests;


use App\Controller\SearchTvShow;
use App\Entity\ExternalApi;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SearchTvShowTest extends WebTestCase
{
    /** @var SearchTvShow */
    private $searchTvShow;

    protected function setUp()
    {
        $client = self::createClient();

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Run the schema update tool using our entity metadata
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropDatabase();
        $schemaTool->updateSchema($metadatas);

        $externalApi = new ExternalApi();
        $externalApi->setUrl('http://api.tvmaze.com/');

        $entityManager->persist($externalApi);
        $entityManager->flush();

        $this->searchTvShow = new SearchTvShow();
        $this->searchTvShow->setContainer($client->getContainer());
    }

    public function testScheduleSuccess()
    {
        $request = new Request();
        $json = $this->searchTvShow->searchSchedule($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('success', $res['code']);
    }

    public function testSearchSpecifyError()
    {
        $request = new Request();
        $json = $this->searchTvShow->searchSpecifyTvShow($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
    }

    public function testSearchSpecifySuccess()
    {
        $request = new Request();
        $request->request->add(['tv', 'girls']);

        $json = $this->searchTvShow->searchSpecifyTvShow($request);
        $res = json_decode($json->getContent(), true);

        $this->assertEquals('error', $res['code']);
    }
}