<?php

namespace App\DataFixtures;

use App\Entity\Etat;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class EtatFixtures extends Fixture
{


    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->createEtats($manager);
        $manager->flush();
    }

    public function createEtats(ObjectManager $manager) {
        $etat = new Etat();
        $etat->setLibelle("Créée");
        $manager->persist($etat);

        $etat2 = new Etat();
        $etat2->setLibelle("Ouverte");
        $manager->persist($etat2);

        $etat3 = new Etat();
        $etat3->setLibelle("Clôturée");
        $manager->persist($etat3);

        $etat4 = new Etat();
        $etat4->setLibelle("Activité en cours");
        $manager->persist($etat4);

        $etat5 = new Etat();
        $etat5->setLibelle("Passée");
        $manager->persist($etat5);

        $etat6 = new Etat();
        $etat6->setLibelle("Annulée");
        $manager->persist($etat6);

        $etat7 = new Etat();
        $etat7->setLibelle("Archivée");
        $manager->persist($etat7);
    }

}