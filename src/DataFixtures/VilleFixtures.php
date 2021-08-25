<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VilleFixtures extends Fixture
{

    public const VILLE1_REFERENCE = 'ville1';
    public const VILLE2_REFERENCE = 'ville2';

    public function load(ObjectManager $manager)
    {
        $ville = new Ville();
        $ville->setNom('Nantes');
        $ville->setCodePostal('44000');
        $manager->persist($ville);
        $manager->flush();

        $this->addReference(self::VILLE1_REFERENCE, $ville);

        $ville = new Ville();
        $ville->setNom('Rennes');
        $ville->setCodePostal('35000');
        $manager->persist($ville);
        $manager->flush();

        $this->addReference(self::VILLE2_REFERENCE, $ville);
    }
}
