<?php

namespace App\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sorties/{id}", name="sortie_afficher")
     */
    public function afficherSorties(int $id, EntityManagerInterface $entityManager): Response
    {

        $sortieRepository = $entityManager -> getRepository(Sortie::class);
        $sortie = $sortieRepository->findBy($id);

        return $this->render('sortie/home.html.twig');
    }
}
