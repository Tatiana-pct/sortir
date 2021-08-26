<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\User;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{

    public const ADMIN_REFERENCE = 'admin-user';
    public const USER_REFERENCE = 'user';

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new Participant();
        $user->setActif(true);
        $user->setAdministrateur(1);
        $user->setPrenom('admin');
        $user->setNom('admin');
        $user->setPseudo('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEmail('admin@admin.com');
        $user->setTelephone('0000000000');
        $password = $this->encoder->encodePassword($user, 'admin');
        $user->setMotDePasse($password);
        $user->setCampus($this->getReference(CampusFixtures::ADMIN_CAMPUS_REFERENCE));

        $manager->persist($user);

        $this->addReference(self::ADMIN_REFERENCE, $user);

        $user = new Participant();
        $user->setActif(true);
        $user->setAdministrateur(0);
        $user->setPrenom('user');
        $user->setNom('user');
        $user->setPseudo('user');
        $user->setEmail('user@user.com');
        $user->setRoles(['ROLE_USER']);
        $password = $this->encoder->encodePassword($user, 'user');
        $user->setMotDePasse($password);
        $user->setTelephone('0000000000');
        $user->setCampus($this->getReference(CampusFixtures::USER_CAMPUS_REFERENCE));
        $manager->persist($user);

        $this->addReference(self::USER_REFERENCE, $user);

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return array(
            CampusFixtures::class,
        );
    }
}
