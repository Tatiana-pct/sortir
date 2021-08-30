<?php

namespace App\Controller;


use App\data\RechercheData;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\rechercheSortieForm;
use App\Form\SortieFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/liste", name="liste")
     */
    public function afficherSorties(EntityManagerInterface $entityManager): Response
    {
        //FIND par état des sortie
        $etatCree = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Création']);
        $etatPubliee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Publiée']);
        $etatAnnulee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']);
        $etatCloturee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Cloturée']);
        $etatEnCours = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'En Cours']);
        $etatTerminee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Terminée']);
        $etatArchivee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'Archivée']);

        //FIND par villes
        $villes = $entityManager->getRepository(Sortie::class)->findAll();

        //Récupération des sortie en fonction de l'état
        $sortiesCrees = $entityManager->getRepository(Sortie::class)->findBy(['etat' => $etatCree, 'Organisateur' => $this->getUser()]);
        $sortiesPubliees = $entityManager->getRepository(Sortie::class)->findBy(['etat' => $etatPubliee]);
        $sortiesAnnulees = $entityManager->getRepository(Sortie::class)->findBy(['etat' => $etatAnnulee]);
        $sortiesCloturees = $entityManager->getRepository(Sortie::class)->findBy(['etat' => $etatCloturee]);
        $sortiesEnCours = $entityManager->getRepository(Sortie::class)->findBy(['etat' => $etatEnCours]);
        $sortiesTerminees = $entityManager->getRepository(Sortie::class)->findBy(['etat' => $etatTerminee]);
        $sortiesArchivees = $entityManager->getRepository(Sortie::class)->findBy(['etat' => $etatArchivee]);

        return $this->render('main/home.html.twig', [
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

    /**
     * @Route("/liste", name="liste")
     */
    public function list(SortieRepository $sortieRepository,
                         Request          $request): Response
    {

        //aller chercher les sorties en BDD

        $user = $this->getUser();

        $data = new RechercheData();

        $sortiesform = $this->createForm(rechercheSortieForm::class, $data);
        $sortiesform->handleRequest($request);
        $sorties = $sortieRepository->trouveData($data);

        return $this->render('sortie/liste.html.twig', ["sorties" => $sorties,
            "user"=>$user,
            'sortiesform'=>$sortiesform->createView()]);
    }


    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieFormType::class, $sortie);

        //traiter le formulaire
        $sortieForm->handleRequest($request);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $sortie->setOrganisateur($user);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $manager = $this->getDoctrine()->getManager();

            if ($sortieForm->get('enregistrer')->isClicked()) {
                $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']));
                $this->addFlash('success', "La sortie a créée!");

            } elseif ($sortieForm->get('publier')->isClicked()) {
                $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']));
                $this->addFlash('warning', "La sortie a été publiée !");
            }

            $manager->persist($sortie);
            $manager->flush();

            return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }


    /**
     * @Route("/details/{id}", name="details")
     */
    public function detail(int $id, SortieRepository $sortieRepository, ParticipantRepository $participantRepository): Response
    {


        $sortie = $sortieRepository->findById($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Oups ! Cette sortie n\'existe pas !');
        }

        $participants = $sortie->getInscrits()->toArray();

        return $this->render('sortie/details.html.twig', ["sortie" => $sortie]);
    }


    /**
     * @Route("/delete/{id}", name="delete")
     *
     */
    public function delete(Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $entityManager->remove($sortie);
        $entityManager->flush();

        $this->addFlash('delete', 'Sortie supprimée');
        return $this->redirectToRoute('sortie_liste');
    }


    /**
     * @Route("/publier/{id}", name="publier")
     */
    public function publier(Sortie $sortie, EntityManagerInterface $em): Response
    {
        $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']));
        $this->addFlash('warning', "La sortie a été publiée !");
        $em->flush();

        $this->addFlash('publiée', 'Sortie publiée');
        return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]);
    }

    /**
     * @Route("/inscription/{id}", name="inscription")
     */
    public function inscrire(int                    $id,
                             EntityManagerInterface $em,
                             UserInterface          $user,
                             ParticipantRepository  $participantRepository,
                             SortieRepository       $sortieRepository): Response
    {
        $participant = $participantRepository->findByMail($user->getUsername());
        $sortie = $sortieRepository->findById($id);


        if ($sortie->getDateLimiteInscription() < new \DateTime()) {
            $this->addFlash('notice', 'La date limite de inscription à la sortie ' . $sortie->getNom() .
                                    ' est dépassé');
        } else {
            $sortie->addInscrit($participant);
            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Vous êtes inscrit à la sortie ' . $sortie->getNom());

        }
        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie]);
    }


    /**
     * @Route("/desister/{id}", name="desister")
     */
    public function desister(int                    $id,
                             EntityManagerInterface $em,
                             UserInterface          $user,
                             ParticipantRepository  $participantRepository,
                             SortieRepository       $sortieRepository): Response
    {
        $participant = $participantRepository->findByMail($user->getUsername());
        $sortie = $sortieRepository->findById($id);

        $sortie->removeInscrit($participant);
        $em->persist($sortie);
        $em->flush();

        $this->addFlash('success', 'Vous n`\'êtes plus inscrit à la sortie ' . $sortie->getNom());

        return $this->render( 'sortie/details.html.twig' , [
            'sortie' => $sortie
        ]);
    }
}