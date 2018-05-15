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
use \PDO;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AppFixtures extends Fixture
{
    /**
     * @var array
     */
    protected $cache = ["series" => [], "authors" => [], "editors" => [], "jobs" => [], ];

    /**
     * @var Connection
     */
    protected $dbCon;

    /**
     * @var string
     */
    protected $env;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AppFixtures constructor.
     * @param ConnectionFixtures $dbCon
     * @param KernelInterface $kernel
     * @param LoggerInterface $logger
     */
    public function __construct(ConnectionFixtures $dbCon, KernelInterface $kernel, LoggerInterface $logger)
    {
        $this->env = $kernel->getEnvironment();
        $this->dbCon = $dbCon->get();
        $this->logger = $logger;
    }

    /**
     * @param ObjectManager $manager
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        // add job (indexed are 0->writer, 1->cartoonist, 2->color)
        foreach (['writer', 'cartoonist', 'color', ] as $jobTitle) {
            $job = (new Job())
                ->setTranslationKey('JOB_' . strtoupper($jobTitle));

            $manager->persist($job);
            $this->cache['jobs'][] = $job;
        }
        $manager->flush(); // save in db and Ids are created

        $dbh = $this->dbCon;

        // add books && author && editor
        $qry = 'SELECT t.* FROM books t ';

        // in test env, we only load 20 books, it's enough to get various cases
        if ($this->env === 'test') {
            $qry .= ' LIMIT 20';
        }

        $q = $dbh->query($qry);
        foreach ($q->fetchAll() as $row) {
            try {
                $book = new Book($this->logger);
                $book->setTitle($row['title']);
                $this->addSerie($row, $book, $dbh, $manager);

                // @todo does this persist mandatory ?
                $manager->persist($book);

                $this->addAuthor($row, $book, $dbh, $manager);
                $this->addEditor($row, $book, $dbh, $manager);

                $manager->persist($book);

                $manager->flush();
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }

    /**
     * @param $bookFixture
     * @param Book $book
     * @param Connection $dbh
     * @param ObjectManager $manager
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function addSerie($bookFixture, Book $book, Connection $dbh, ObjectManager $manager)
    {
        $bookId = $bookFixture['id'];

        // add serie
        $sth = $dbh->prepare(
            <<<SQL
SELECT m.id, m.name
FROM books_series_link AS t
INNER JOIN series AS m ON t.series = m.id
WHERE book = :bookId
SQL
        );
        $sth->bindParam('bookId', $bookId, PDO::PARAM_INT);
        $sth->execute();
        $row = $sth->fetch();

        if ($row) {
            if (!in_array($row['id'], $this->cache['series'])) {
                $serie = (new Serie())
                    ->setName($row['name']);
                $manager->persist($serie);

                $this->cache['series'][] = $row['id'];
            } else {
                $serie = $manager
                    ->getRepository('\\App\\Entity\\Library\\Serie')
                    ->findOneBy(['name' => $row['name']]);
            }

            $book->setSerie($serie)
                ->setIndexInSerie($bookFixture['series_index']);
        }
    }

    /**
     * @param $bookFixture
     * @param Book $book
     * @param Connection $dbh
     * @param ObjectManager $manager
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function addEditor($bookFixture, Book $book, Connection $dbh, ObjectManager $manager)
    {
        $bookId = $bookFixture['id'];

        // add editor
        $sth = $dbh->prepare(
            <<<SQL
SELECT m.id, m.name
FROM books_publishers_link AS t
INNER JOIN publishers AS m ON t.publisher = m.id
WHERE book = :bookId
SQL
        );
        $sth->bindParam('bookId', $bookId, PDO::PARAM_INT);
        $sth->execute();
        $row = $sth->fetch();

        if ($row) {
            if (!in_array($row['id'], $this->cache['editors'])) {
                $editor = (new Editor())
                    ->setName($row['name']);
                $manager->persist($editor);

                $this->cache['editors'][] = $row['id'];
            } else {
                $editor = $manager
                    ->getRepository('\\App\\Entity\\Library\\Editor')
                    ->findOneBy(['name' => $row['name']]);
            }

            $dateTime = new \DateTime($bookFixture['pubdate']);
            $book->addEditor($editor, $dateTime, $bookFixture['isbn']);
        }
    }

    /**
     * @param $bookFixture
     * @param Book $book
     * @param Connection $dbh
     * @param ObjectManager $manager
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function addAuthor($bookFixture, Book $book, Connection $dbh, ObjectManager $manager)
    {
        $rows = $this->getAuthorsInfo($bookFixture, $dbh);

        if (!count($rows)) {
            return;
        }

        $i = 0;
        foreach ($rows as $row) {
            $authorName = explode('| ', $row['name']);
            if (!in_array($row['id'], $this->cache['authors'])) {
                $author = $this->getNewAuthor($manager, $authorName, $row);
            } else {
                $author = $this->getExistingAuthor($manager, $authorName);
            }

            $this->attachAuthorToBook($book, $author, $i);

            $i++;
        }
    }

    /**
     * @param $bookFixture
     * @param Connection $dbh
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getAuthorsInfo($bookFixture, Connection $dbh): array
    {
        $bookId = $bookFixture['id'];

        // add author
        $sth = $dbh->prepare(
            <<<SQL
SELECT m.id, m.name
FROM books_authors_link AS t
INNER JOIN authors AS m ON t.author = m.id
WHERE book = :bookId
ORDER BY t.id
SQL
        );
        $sth->bindParam('bookId', $bookId, PDO::PARAM_INT);
        $sth->execute();
        $rows = $sth->fetchAll();
        return $rows;
    }

    /**
     * @param ObjectManager $manager
     * @param $authorName
     * @param $row
     * @return Author
     */
    protected function getNewAuthor(ObjectManager $manager, $authorName, $row): Author
    {
        $author = new Author();
        if (2 === count($authorName)) {
            $author->setFirstname($authorName[0])
                ->setLastname($authorName[1]);
        } else {
            $author->setFirstname($authorName[0]);
        }

        $manager->persist($author);

        $this->cache['authors'][] = $row['id'];
        return $author;
    }

    /**
     * @param ObjectManager $manager
     * @param $authorName
     * @return null|object
     */
    protected function getExistingAuthor(ObjectManager $manager, $authorName)
    {
        $criterias = ['firstname' => $authorName[0], 'lastname' => null,];
        if (2 === count($authorName)) {
            $criterias['lastname'] = $authorName[1];
        }
        $author = $manager
            ->getRepository('\\App\\Entity\\Library\\Author')
            ->findOneBy($criterias);
        return $author;
    }

    /**
     * @param Book $book
     * @param $author
     * @param $i
     */
    protected function attachAuthorToBook(Book $book, $author, $i): void
    {
        $job = $this->cache['jobs'][0];
        if (1 === $i) {
            $job = $this->cache['jobs'][0];
        }
        $book->addAuthor($author, $job);
    }
}
