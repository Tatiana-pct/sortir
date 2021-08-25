<?php

namespace App\DataFixtures;

use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LieuxFixtures extends Fixture implements DependentFixtureInterface
{

    public const LIEUX_REFERENCE = "lieux";

    public function load(ObjectManager $manager)
    {
        $lieux = new Lieu();
        $lieux->setNom('Divers');
        $lieux->setRue('rue on sait pas où');
        $lieux->setLatitude(43.510721);
        $lieux->setLongitude(0.437122);
        $lieux->setVille($this->getReference(VilleFixtures::VILLE1_REFERENCE));
        $manager->persist($lieux);

        $this->addReference(self::LIEUX_REFERENCE, $lieux);

        $lieux = new Lieu();
        $lieux->setNom('ENI');
        $lieux->setRue('avenue Léo Lagrange');
        $lieux->setLatitude(46.316412);
        $lieux->setLongitude(-0.471082);
        $lieux->setVille($this->getReference(VilleFixtures::VILLE2_REFERENCE));
        $manager->persist($lieux);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            VilleFixtures::class,
        );
    }
}
