<?php

namespace App\Controller;


use App\Entity\Campus;
use App\Form\CampusType;
use App\Form\RechercheCampusFormType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/campus", name="campus_")
 */
class CampusController extends AbstractController
{
    /**
     * @var Campus[]|array|object[]
     */
    private $CampusList= null;

    /**
     * @Route("/liste", name="liste")
     */
    public function afficherCampus(CampusRepository $CampusRepository, Request $request, EntityManagerInterface $entityManager)

    {
        //affichage de la list des campus
        $campus= $CampusRepository->findAll();
        $Campus = new Campus();

        //creation du formulaire d'ajout
        $CampusForm =$this->createForm(CampusType::class,$Campus);
        $CampusForm->handleRequest($request);

        //validation du formulaire d'ajout d'une ville
        if($CampusForm->isSubmitted() && $CampusForm->isValid()) {
            $entityManager->persist($Campus);
            $entityManager->flush();

        //affichage d'un message lors du succes d'ajout d'une ville
            $this->addFlash('succes','Campus ajouté!');

        //retirection vers la page
            return $this->redirectToRoute('campus_liste');
        }

        //declaration du formulaire de recherche
        $rechercheCampusForm = $this->createForm(RechercheCampusFormType::class, $Campus);
        $rechercheCampusForm->handleRequest($request);

        //validation du formulaire de recherched'une ville
        if ($rechercheCampusForm->isSubmitted()&&$rechercheCampusForm->isValid()) {
            $entityManager->persist($Campus);
            $entityManager->flush();

            //rediection de la reponse
            return $this->redirectToRoute('campus_liste');
        }

            return $this->render('campus/list.html.twig', [
                "Campus" => $campus,
                "CampusForm" => $CampusForm->createView(),

            ]);

    }





    /**
     * @Route("/edit", name="edit")
     */
    public function editCampus(int $id, EntityManagerInterface $entityManager, Request $request)
    {
        $Campus = new Campus();
        $form = $this->createForm(CampusType::class, $Campus);
        $form->remove('submit');
        $form->add('submit',SubmitType::class,[
            'label' => 'modifier',

        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){
            $Campus =$form->getData();

            $entityManager->persist($Campus);
            $entityManager->flush();
            $this->addFlash('Succes','la ville a bien ete modifiée !');
            $this->CampusList =$entityManager->getRepository(Campus::class)->findAll();
            return  $this->redirectToRoute('villes_');
        }



        return $this->render('campus/list.html.twig');
    }


    /**
     * @Route("/delete", name="delete")
     */
    public function deleteCampus(int $id, EntityManagerInterface $entityManager)
    {
        $campus= new Campus();

        $entityManager->remove($campus);
        $entityManager->flush();
        return $this->redirectToRoute('campus_liste');
    }


}