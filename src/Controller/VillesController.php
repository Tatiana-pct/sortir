<?php

namespace App\Controller;




use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/villes", name="villes_")
 */
class VillesController extends AbstractController
{
    /**
     * @Route("/liste", name="liste")
     */
    public function afficherVille(VilleRepository $villeRepository)
    {
        $ville =$villeRepository->findAll();

        return $this->render('villes/list.html.twig',[
            "ville" => $ville
        ]);

    }


    /**
     * @Route("/create", name="create")
     */
    public function createVille()
    {
        return $this->render('villes/create.html.twig');
    }


    /**
     * @Route("/edit", name="edit")
     */
    public function editVille(int $id, EntityManagerInterface $entityManager)
    {
        $ville = new ville();
        $ville->setNom('');

        return $this->render('villes/create.html.twig');
    }


    /**
     * @Route("/delete", name="delete")
     */
    public function DeleteVille(int $id, EntityManagerInterface $entityManager)
    {
        $ville= new ville();

        $entityManager->remove($villes);
        $entityManager->flush();
        return $this->render('villes/create.html.twig');
    }
}