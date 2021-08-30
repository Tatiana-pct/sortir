<?php

namespace App\Controller;


use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/campus", name="campus_")
 */
class CampusController extends AbstractController
{

    /**
     * @Route("/liste", name="liste")
     */
    public function afficherCampus(CampusRepository $CampusRepository, Request $request, EntityManagerInterface $entityManager)

    {
        $campus= $CampusRepository->findAll();
        $Campus = new Campus();
        $CampusForm =$this->createForm(CampusType::class,$Campus);
        $CampusForm->handleRequest($request);

        if($CampusForm->isSubmitted() && $CampusForm->isValid()) {
            $entityManager->persist($Campus);
            $entityManager->flush();

            $this->addFlash('succes','Campus ajouté!');
            return $this->redirectToRoute('campus_liste');
        }

        return $this->render('campus/list.html.twig',[
            "Campus" => $campus,
            "CampusForm" => $CampusForm->createView(),

    ]);

    }


    /**
     * @Route("/create", name="create")
     *
     */
    public function createCampus(CampusRepository $campusRepository)
    {
        return $this->render('campus/list.html.twig');
    }


    /**
     * @Route("/edit", name="edit")
     */
    public function editCampus(int $id, EntityManagerInterface $entityManager)
    {
        $campus= new Campus();
        $campus->setNom('');

        return $this->render('campus/create.html.twig');
    }


    /**
     * @Route("/delete", name="delete")
     */
    public function deleteCampus(int $id, EntityManagerInterface $entityManager)
    {
        $campus= new Campus();

        $entityManager->remove($campus);
        $entityManager->flush();
        return $this->render('campus/create.html.twig');
    }


}