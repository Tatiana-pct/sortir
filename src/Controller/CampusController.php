<?php

namespace App\Controller;


use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

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
    public function afficherCampus(CampusRepository  $campusRepository)

    {
        $campus = $campusRepository->findAll();

        return $this->render('campus/list.html.twig',[
        "campus" => $campus
    ]);

    }
    /**
     * @Route("/demo", name="em-demo")
     */
    public function demo(EntityManagerInterface $entityManager)

    {
        $campus= new Campus();

        $campus->setNom('bdx');
        dump($campus);

        $entityManager->persist();
        $entityManager->flush();





        return $this->render('campus/list.html.twig');
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
    public function DeleteCampus(int $id, EntityManagerInterface $entityManager)
    {
        $campus= new Campus();

        $entityManager->remove($campus);
        $entityManager->flush();
        return $this->render('campus/create.html.twig');
    }


}