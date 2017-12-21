<?php
namespace App\DataFixtures\Library;

use App\Entity\Library\Author;
use App\Entity\Library\Editor;
use App\Entity\Library\Job;
use App\Entity\Library\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // add job
        foreach (['cartoonist', 'writer', 'color', ] as $jobTitle) {
            $job = (new Job())
                ->setRole($jobTitle)
                ->setTanslationKey('JOB_'.strtoupper($jobTitle));

            $manager->persist($job);
        }

        $dbh = new \PDO('sqlite:///' . __DIR__ . '/../../../var/data/fixtures.db');

        // add author
        $q = $dbh->query('SELECT t.* FROM authors t LIMIT 10');
        foreach ($q as $row) {
            list($fname, $lname) = split('| ', $row['name']);
            $author = (new Author())
                ->setFirstname($fname)
                ->setLastname($lname);

            $manager->persist($author);
        }
        unset($q, $row);

        // add serie
        $q = $dbh->query('SELECT t.* FROM series t LIMIT 10');
        foreach ($q as $row) {
            $serie = (new Serie())
                ->setName($row['name']);

            $manager->persist($serie);
        }
        unset($q, $row);

        // add editor
        $q = $dbh->query('SELECT t.* FROM publishers t LIMIT 10');
        foreach ($q as $row) {
            $editor = (new Editor())
                ->setName($row['name']);

            $manager->persist($editor);
        }
        unset($q, $row);

        // add book


        $manager->flush();
    }
}
