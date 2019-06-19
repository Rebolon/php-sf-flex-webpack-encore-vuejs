<?php
namespace App\DataFixtures\Library;

use App\DataFixtures\ConnectionFixtures;
use App\Entity\Library\Author;
use App\Entity\Library\Book;
use App\Entity\Library\Editor;
use App\Entity\Library\Job;
use App\Entity\Library\Loan;
use App\Entity\Library\Reader;
use App\Entity\Library\Serie;
use App\Entity\Library\Tag;
use DateInterval;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Exception;
use \PDO;
use Symfony\Component\HttpKernel\KernelInterface;

class AppFixtures extends Fixture
{
    /**
     * @var array
     */
    protected $cache = ["series" => [], "tags" => [], "authors" => [], "editors" => [], "jobs" => [], ];

    /**
     * @var Connection
     */
    protected $dbCon;

    /**
     * @var string
     */
    protected $env;

    /**
     * AppFixtures constructor.
     * @param ConnectionFixtures $dbCon
     * @param KernelInterface $kernel
     */
    public function __construct(ConnectionFixtures $dbCon, KernelInterface $kernel)
    {
        $this->env = $kernel->getEnvironment();
        $this->dbCon = $dbCon->get();
    }

    /**
     * @param ObjectManager $manager
     * @throws \Doctrine\DBAL\DBALException
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        // init some vars
        $loans = [];

        // add job (indexed are 0->writer, 1->cartoonist, 2->color)
        foreach (['writer', 'cartoonist', 'color', ] as $jobTitle) {
            $job = (new Job())
                ->setTranslationKey('JOB_' . strtoupper($jobTitle));

            $manager->persist($job);
            $this->cache['jobs'][] = $job;
        }
        $manager->flush(); // save in db and Ids are created

        $dbh = $this->dbCon;

        // add main reader
        $readers[] = new Reader();
        $readers[0]->setFirstname('John')
            ->setLastname('Doe');

        // add extra readers
        foreach ([
            ['fname' => 'Jane', 'lname' => 'Smith', ],
            ['fname' => 'Antony', 'lname' => 'Durand', ],
         ] as $newReader) {
            $readers[] = new Reader();
            $readers[count($readers)-1]
                ->setFirstname($newReader['fname'])
                ->setLastname($newReader['lname']);
        }

        // add books && author && editor
        $qry = 'SELECT t.* FROM books t ';

        // in test env, we only load 20 books, it's enough to get various cases
        if ($this->env === 'test') {
            $qry .= ' LIMIT 20';
        }

        $q = $dbh->query($qry);
        foreach ($q->fetchAll() as $idx => $row) {
            try {
                $book = new Book();
                $book->setTitle($row['title']);

                $this->addSerie($row, $book, $dbh, $manager);
                $this->addTags($row, $book, $dbh, $manager);
                $this->addAuthor($row, $book, $dbh, $manager);
                $this->addEditor($row, $book, $dbh, $manager);

                $loans = $this->attachReadersAndLoans($readers, $book, $idx, $loans);

                $manager->persist($book);
                $manager->flush();
            } catch (Exception $e) {
                throw $e;
            }
        }

        try {
            foreach ($readers as $reader) {
                $manager->persist($reader);
                $manager->flush();
            }

            foreach ($loans as $loan) {
                $manager->persist($loan);
                $manager->flush();
            }
        } catch (Exception $e) {
            throw $e;
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
    protected function addTags($bookFixture, Book $book, Connection $dbh, ObjectManager $manager)
    {
        $bookId = $bookFixture['id'];

        // add serie
        $sth = $dbh->prepare(
            <<<SQL
SELECT m.id, m.name
FROM books_tags_link AS t
INNER JOIN tags AS m ON t.tag = m.id
WHERE t.book = :bookId
SQL
        );
        $sth->bindParam('bookId', $bookId, PDO::PARAM_INT);
        $sth->execute();
        $row = $sth->fetch();

        if ($row) {
            if (!in_array($row['id'], $this->cache['tags'])) {
                $tag = (new Tag())
                    ->setName($row['name']);
                $manager->persist($tag);

                $this->cache['tags'][] = $row['id'];
            } else {
                $tag = $manager
                    ->getRepository(Tag::class)
                    ->findOneBy(['name' => $row['name']]);
            }

            if ($tag) {
                $book->addTag($tag);
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

            $dateTime = new DateTime($bookFixture['pubdate']);
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

    /**
     * @param Reader[]|array $readers
     * @param Book $book
     * @param $idx
     * @return Loan|array
     * @throws Exception
     */
    protected function attachReadersAndLoans($readers, Book $book, $idx, $loans)
    {
        $readers[0]->addMyLibrary($book);

        if ($idx === 0) {
            $readers[1]->addMyLibrary($book);
            $readers[2]->addMyLibrary($book);
        }

        // an ended loan from reader 0 to 1
        if ($idx === 1) {
            $readers[1]->addMyLibrary($book);

            $startLoan = new DateTime();
            $startLoan->setDate(2019, 1, 2);
            $endLoan = clone $startLoan;
            $endLoan->add(new DateInterval('P40D'));

            $loan = new Loan();
            $loan->setBook($book)
                ->setLoaner($readers[0])
                ->setBorrower($readers[1])
                ->setStartLoan($startLoan)
                ->setEndLoan($endLoan);

            $loans[] = $loan;
        }

        // an ended loan from 1 to 2
        if ($idx === 2) {
            $readers[1]->addMyLibrary($book);

            $startLoan = new DateTime();
            $startLoan->setDate(2019, 2, 10);
            $endLoan = clone $startLoan;
            $endLoan->add(new DateInterval('P33D'));

            $loan = new Loan();
            $loan->setBook($book)
                ->setLoaner($readers[1])
                ->setBorrower($readers[2])
                ->setStartLoan($startLoan)
                ->setEndLoan($endLoan);

            $loans[] = $loan;
        }

        // a pending loan
        if ($idx === 3) {
            $startLoan = new DateTime();
            $startLoan->setDate(2019, 3, 22);

            $loan = new Loan();
            $loan->setBook($book)
                ->setLoaner($readers[0])
                ->setBorrower($readers[1])
                ->setStartLoan($startLoan);

            $loans[] = $loan;
        }

        return $loans;
    }
}
