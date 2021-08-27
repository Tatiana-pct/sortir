<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
       $etatCree = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Création']);
       $etatPubliee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Publiée']);
       $etatAnnulee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Annulée']);
       $etatCloturee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Cloturée']);
       $etatEnCours = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'En Cours']);
       $etatTerminee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Terminée']);
       $etatArchivee = $entityManager->getRepository(Etat::class)->findOneBy(['libelle'=>'Archivée']);

       //FIND par villes
        $villes = $entityManager->getRepository(Sortie::class)->findAll();

        //Récupération des sortie en fonction de l'état
        $sortiesCrees = $entityManager->getRepository(Sortie::class)->findBy(['etat' => $etatCree, 'Organisateur' => $this->getUser()]);
        $sortiesPubliees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatPubliee]);
        $sortiesAnnulees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatAnnulee]);
        $sortiesCloturees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatCloturee]);
        $sortiesEnCours = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatEnCours]);
        $sortiesTerminees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatTerminee]);
        $sortiesArchivees = $entityManager->getRepository(Sortie::class)->findBy(['etat' =>$etatArchivee]);

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
     * @Route("/create", name="create")
     */
    public function create(Request $request,EntityManagerInterface $em): Response
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

            if($sortieForm->get('enregistrer')->isClicked()){
                $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']));
                $this->addFlash('success', "La sortie a créée!");

            } elseif ($sortieForm->get('publier')->isClicked()){
                $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']));
                $this->addFlash('warning', "La sortie a été publiée !");
            }

            $manager->persist($sortie);
            $manager->flush();

            return $this->redirectToRoute('sortie_details', ['id'=>$sortie->getId()]);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    //TODO: fonction detail + template
    /**
     * @Route("/details/{id}", name="details")
     */
        public function detail(int $id, SortieRepository $sortieRepository):Response
        {
            $sortie = $sortieRepository->find($id);
            if(!$sortie) {
                throw $this->createNotFoundException('Oups ! Cette sortie n\'existe pas !');
            }
            return $this->render('sortie/details.html.twig', ["sortie"=>$sortie]);
        }

    /*
        public function detail(int $id, SortieRepository $sortieRepository): Response
        {
            $sortie = $sortieRepository->find($id);
            return $this->render('wish/detail.html.twig', ["sortie" => $sortie]);
        }*/
}