<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{

    public const ADMIN_CAMPUS_REFERENCE = 'admin-campus';
    public const USER_CAMPUS_REFERENCE = 'user-campus';

    public function load(ObjectManager $manager)
    {
        $campus = new Campus();
        $campus->setNom('Nantes');
        $manager->persist($campus);

        $this->addReference(self::USER_CAMPUS_REFERENCE, $campus);

        $campus = new Campus();
        $campus->setNom('Rennes');
        $manager->persist($campus);
        $manager->flush();

        $this->addReference(self::ADMIN_CAMPUS_REFERENCE, $campus);
    }
}
