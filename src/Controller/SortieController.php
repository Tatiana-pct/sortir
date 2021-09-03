<?php

namespace App\Controller;


use App\data\RechercheData;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\rechercheSortieForm;
use App\Form\SortieFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
     * @throws Exception
     */
    public function list(SortieRepository $sortieRepository,
                         Request          $request,
                         EntityManagerInterface $em): Response
    {

        $user = $this->getUser();
        $manager = $this->getDoctrine()->getManager();

        $data = new RechercheData();

        $sortiesform = $this->createForm(rechercheSortieForm::class, $data);
        $sortiesform->handleRequest($request);
        $sorties = $sortieRepository->trouveData($data, $user);

        foreach ($sorties as $sortie) {
            $intervalSortie = $sortie->getDuree();

            $dureeSortie = new DateInterval('PT' . $intervalSortie . 'M');

            $now = new DateTime("now");

            if ($sortie->getDateHeureDebut()== $now) {
                $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Activité en cours']));
            } elseif (($sortie->getDateHeureDebut()->add($dureeSortie))<= $now){
                $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Passée']));
                if ($sortie->getDateHeureDebut() == ($sortie->getDateHeureDebut()->add($dureeSortie))) {
                    $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Archivée']));
                }
            }
        }

        return $this->render('sortie/liste.html.twig', ["sorties" => $sorties,
            "user" => $user,
            'sortiesform' => $sortiesform->createView()]);
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
        $now = date('Y-m-d', strtotime('now'));
        if ($sortie->getDateHeureDebut() > $now) {

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
        } else {
            $this->addFlash('alerte', 'La sortie ne peut pas être avant demain !');
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
    public function delete(Sortie $sortie, EntityManagerInterface $em): Response
    {
        if ($sortie->getEtat()->getLibelle()!= 'Ouverte') {

            $this->addFlash('notice', 'Cette sortie n\'est pas ouverte et ne peux donc pas être annulée !');
        } else {
            $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']));
            $em->flush();

            $this->addFlash('delete', 'Sortie supprimée');

        }
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
        $nbInscrit = $sortie->getInscrits()->count();

        $now = date('d-m-Y', strtotime('now'));
        if ($sortie->getDateLimiteInscription()  <  $now) {

            $this->addFlash('notice', 'La date limite de inscription à la sortie ' . $sortie->getNom() .
                                    ' est dépassé');
        } elseif ($nbInscrit == $sortie->getNbInscriptionsMax()) {

            $this->addFlash('notice', 'Cette sortie est déjà complète');
            $sortie->setEtat($em->getRepository(Etat::class)->findById(3));
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
        return $this->redirectToRoute('sortie_liste');
//        return $this->render('sortie/details.html.twig', [
//            'sortie' => $sortie
//        ]);
//
    }

    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifierSortie(int $id,
                                   Request $request,
                                   EntityManagerInterface $em,
                                   UserInterface          $user,
                                   ParticipantRepository  $participantRepository,
                                   SortieRepository       $sortieRepository) : Response
    {
        $sortie = $sortieRepository->findById($id);
        $user = $this->getUser();

        $modifierSortieForm = $this->createForm(SortieFormType::class, $sortie);
        $modifierSortieForm->handleRequest($request);

        if($user != $sortie->getOrganisateur()) {
            $this->addFlash('fail', 'Vous ne pouvez pas modifier cette sortie !');
        }

        if ($modifierSortieForm->isSubmitted()) {
            if ($modifierSortieForm->isValid()) {

                $em->persist($sortie);
                $em->flush();

                $this->addFlash('success', 'Sortie modifié !');
                return $this->redirectToRoute('sortie_details', ['id' => $sortie->getId()]
                );
            } else {
                $em->refresh($sortie);
            }
        }
        return $this->render('sortie/modifier.html.twig', [
            'registrationForm' => $modifierSortieForm->createView(),
            'user' => $user,
            'sortie'=>$sortie
        ]);
    }
}

