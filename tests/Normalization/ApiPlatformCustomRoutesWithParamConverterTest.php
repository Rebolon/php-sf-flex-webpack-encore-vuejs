<?php
/**
 * run it with phpunit --group git-pre-push
 */
namespace App\Tests\Normalization;

use App\Tests\Common\ApiAbstract;
use App\Tests\Common\JsonBook;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Quick test on all en page that should return at least 200 OK + some other checks
 *
 * Take care : those tests depends on DB for instance, and on parameters.yml (test_MYKEY)
 * We have to use fixtures or SQLITE dbs with required data to make the app run in test mode (or mock everything)
 */
class ApiPlatformCustomRoutesWithParamConverterTest extends ApiAbstract
{
    /**
     * @group git-pre-push
     */
    public function testBookSpecialSample3WithAllEntitiesToBeCreated()
    {
        $client = $this->getClient();
        $router = $this->getRouter();
        // $uri = $router->generate('book_special_sample3', []);
        // router fails to generate the route so for instance don't loose time and force uri
        $uri = '/api/booksiu/special_3';
        $content = JsonBook::$bodyOk;
        $expected = json_decode($content);

        $client->request('POST', $uri, [], [], [], $content);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(201, $response->getStatusCode());

        // @todo test the json return
        /**
         * {
         *   "id": 1,
         *   "title": "Zombies in western culture",
         *   "description": null,
         *   "indexInSerie": null,
         *   "reviews": [],
         *   "serie": "/api/series/1",
         *   "authors": ["/api/project_book_creation/1", "/api/project_book_creation/2", ],
         *   "editors": ["/api/project_book_creation/1", "/api/project_book_creation/2",]
         * }
         */
        $dbResult = $this->dbCon->fetchAll('SELECT * FROM book WHERE id = ' . $responseData['id']);
        $this->assertCount(1, $dbResult);
        $this->assertEquals($responseData['id'], $dbResult[0]['id']);
        $this->assertEquals($expected->book->title, $responseData['title']);
        $this->assertEquals($dbResult[0]['serie_id'], $responseData['serie']['id']);

        $this->assertEquals($responseData['title'], $dbResult[0]['title']);

        $dbResult = $this->dbCon->fetchAll('SELECT * FROM project_book_creation WHERE book_id = ' . $responseData['id']);
        $this->assertCount(2, $dbResult);
        $this->assertNotEquals($dbResult[0]['author_id'], $dbResult[1]['author_id']);

        foreach ($dbResult as $authors) {
            $dbAuthorResult = $this->dbCon->fetchAll('SELECT * FROM author WHERE id = ' . $authors['author_id']);
            $this->assertCount(1, $dbAuthorResult);
        }

        $dbResult = $this->dbCon->fetchAll('SELECT * FROM project_book_edition WHERE book_id = ' . $responseData['id']);
        $this->assertCount(2, $dbResult);
        $this->assertEquals($dbResult[0]['editor_id'], $dbResult[1]['editor_id']);

        foreach ($dbResult as $editors) {
            $dbEditorResult = $this->dbCon->fetchAll('SELECT * FROM editor WHERE id = ' . $editors['editor_id']);
            $this->assertCount(1, $dbEditorResult);
        }

        return;
    }

    /**
     * @group git-pre-push
     */
    public function testBookSpecialSample3WithReuseOfEntityFromDoctrine()
    {
        $client = $this->getClient();
        $router = $this->getRouter();
        // $uri = $router->generate('book_special_sample3', []);
        // router fails to generate the route so for instance don't loose time and force uri
        $uri = '/api/booksiu/special_3';
        $content = JsonBook::$bodyOkWithExistingEntities;
        $expected = json_decode($content);

        $client->request('POST', $uri, [], [], [], $content);

        $response = $client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertSame(201, $response->getStatusCode());

        // @todo test the json return
        /**
         * {
         *   "id": 1,
         *   "title": "Oh my god, how simple it is !",
         *   "description": null,
         *   "indexInSerie": null,
         *   "reviews": [],
         *   "serie": "/api/series/4",
         *   "authors": ["/api/project_book_creation/1", "/api/project_book_creation/2", ],
         *   "editors": ["/api/project_book_creation/1", "/api/project_book_creation/2",]
         * }
         */
        $dbResult = $this->dbCon->fetchAll('SELECT * FROM book WHERE id = ' . $responseData['id']);
        $this->assertEquals($dbResult[0]['serie_id'], $expected->book->serie);

        $this->assertEquals($expected->book->title, $dbResult[0]['title']);

        $dbResult = $this->dbCon->fetchAll('SELECT * FROM project_book_creation WHERE book_id = ' . $responseData['id']);
        $this->assertCount(2, $dbResult);
        $this->assertNotEquals($dbResult[0]['author_id'], $dbResult[1]['author_id']);
        // check that it has used existing record
        $this->assertEquals($expected->book->authors[0]->author, $dbResult[0]['author_id']);

        $dbResult = $this->dbCon->fetchAll('SELECT * FROM project_book_edition WHERE book_id = ' . $responseData['id']);
        $this->assertCount(2, $dbResult);
        $this->assertNotEquals($dbResult[0]['editor_id'], $dbResult[1]['editor_id']);
        // check that it has used existing record
        $this->assertEquals($expected->book->editors[0]->editor, $dbResult[0]['editor_id']);

        return;
    }

    /**
     * @group git-pre-push
     */
    public function testBookSpecialSample3WithErrors()
    {
        $client = $this->getClient();
        $router = $this->getRouter();
        // $uri = $router->generate('book_special_sample3', []);
        // router fails to generate the route so for instance don't loose time and force uri
        $uri = '/api/booksiu/special_3';
        $content = JsonBook::$bodyNoEditor;
        $expected = json_decode(<<<JSON
{
  "type": "https:\/\/tools.ietf.org\/html\/rfc2616#section-10",
  "title": "An error occurred",
  "detail": "book.editors[0].editor: jsonOrArray for editor must be string or array",
  "violations":[{
    "propertyPath": "book.editors[0].editor",
    "message": "jsonOrArray for editor must be string or array"
  }]
}
JSON
);

        $client->request('POST', $uri, [], [], [], $content);
        $response = $client->getResponse();
        $responseData = json_decode($response->getContent());

        $this->assertSame(400, $response->getStatusCode());
        $this->assertEquals($expected->type, $responseData->type);
        $this->assertEquals($expected->title, $responseData->title);
        $this->assertEquals($expected->detail, $responseData->detail);
        $this->assertEquals($expected->violations[0]->propertyPath, $responseData->violations[0]->propertyPath);
        $this->assertEquals($expected->violations[0]->message, $responseData->violations[0]->message);

        return;
    }
}
