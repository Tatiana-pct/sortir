<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sorties/", name="sortie_liste")
     */
    public function afficherSorties(EntityManagerInterface $entityManager): Response
    {
        //FIND par état des sorties
       $etatCree = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Création']);
       $etatPubliee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Publiée']);
       $etatAnnulee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Annulée']);
       $etatCloturee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Cloturée']);
       $etatEnCours = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'En Cours']);
       $etatTerminee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Terminée']);
       $etatArchivee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Archivée']);

       //FIND par villes
        $villes = $entityManager->getRepository(Sortie::class)->findAll();

        //Récupération des sorties en fonction de l'état
        $sortiesCrees = $entityManager->getRepository(Sortie::class)->findBy(['etat' => $etatCree, 'Organisateur' => $this->getUser()]);
        $sortiesPubliees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatPubliee]);
        $sortiesAnnulees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatAnnulee]);
        $sortiesCloturees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatCloturee]);
        $sortiesEnCours = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatEnCours]);
        $sortiesTerminees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatTerminee]);
        $sortiesArchivees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatArchivee]);

        return $this->render('home.html.twig', [
            'nomController' => 'SortieController',
            'sortiesCréees' => $sortiesCrees,
            'sortiesPubliées' => $sortiesPubliees,
            'sortiesAnnulées' => $sortiesAnnulees,
            'sortiesCloturées' => $sortiesCloturees,
            'sortiesEnCours' => $sortiesEnCours,
            'sortiesTerminées' => $sortiesTerminees,
            'sortiesArchivées' => $sortiesArchivees
        ]);
    }
}
