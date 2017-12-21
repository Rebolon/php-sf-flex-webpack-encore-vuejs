<?php
namespace App\DataFixtures\Library;

use App\DataFixtures\ConnectionFixtures;
use App\Entity\Library\Author;
use App\Entity\Library\Book;
use App\Entity\Library\Editor;
use App\Entity\Library\Job;
use App\Entity\Library\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;

class AppFixtures extends Fixture
{
    /**
     * @var Connection
     */
    protected $dbCon;

    public function __construct(ConnectionFixtures $dbCon)
    {
        $this->dbCon = $dbCon->get();
    }

    public function load(ObjectManager $manager)
    {
        // add job
        $jobs = [];
        foreach (['writer', 'cartoonist', 'color', ] as $jobTitle) {
            $job = (new Job())
                ->setRole($jobTitle)
                ->setTanslationKey('JOB_'.strtoupper($jobTitle));

            $manager->persist($job);
            $jobs[$jobTitle] = $job;
        }

        $dbh = $this->dbCon;

        // add books && author && editor
        $q = $dbh->query('SELECT t.* FROM books t LIMIT 50');
        foreach ($q->fetchAll() as $row) {
            try {
                $book = new Book();
                $book->setTitle($row['title']);
                $this->addSerie($row, $book, $dbh, $manager);
                
                $manager->persist($book);

                if ($row['author_sort']) {
                    $dataAuthors = explode('& ', $row['author_sort']);
                    $i = 0;
                    foreach ($dataAuthors as $authorNames) {
                        $authorName = explode(', ', $authorNames);
                        $author = new Author();
    
                        if (count($authorName) === 2) {
                            $author->setLastname($authorName[0])
                                ->setFirstname($authorName[1]);
                        } else {
                            $author->setFirstname($authorName[0]);
                        }
    
                        $manager->persist($author);
    
                        $job = $jobs['writer'];
                        if ($i === 1) {
                            $job = $jobs['cartoonist'];
                        }
                        $book->addAuthor($author, $job);
                    }
                }

                $this->addEditor($row, $book, $dbh, $manager);
                
                $manager->persist($book);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        $manager->flush();
    }

    protected function addSerie($bookFixture, Book $book, Connection $dbh, ObjectManager $manager)
    {
        $bookId = $bookFixture['id'];

        // add serie
        $qSerie = $dbh->query(
            <<<SQL
SELECT m.id, m.name
FROM books_series_link AS t
INNER JOIN series AS m ON t.series = m.id
WHERE book = $bookId
SQL
        );
        $rowSerie = $qSerie->fetch();
        if ($rowSerie) {
            $serie = (new Serie())
                ->setName($rowSerie['name']);
            $manager->persist($serie);
            $book->setSerie($serie)
                ->setIndexInSerie($bookFixture['series_index']);
        }
    }

    protected function addEditor($bookFixture, Book $book, Connection $dbh, ObjectManager $manager)
    {
        $bookId = $bookFixture['id'];

        // add editor
        $q = $dbh->query(
            <<<SQL
SELECT m.id, m.name
FROM books_publishers_link AS t
INNER JOIN publishers AS m ON t.publisher = m.id
WHERE book = $bookId
SQL
        );
        $row = $q->fetch();
        if ($row) {
            $editor = (new Editor())
                ->setName($row['name']);
            $manager->persist($editor);
            $dateTime = new \DateTime($bookFixture['pubdate']);
            $book->addEditor($editor, $dateTime, $bookFixture['isbn']);
        }
    }

}
