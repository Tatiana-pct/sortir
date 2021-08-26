<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
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
    public function afficherVilles()
    {
        return $this->render('villes/list.html.twig');
    }


    /**
     * @Route("/create", name="create")
     */
    public function createVille()
    {
        return $this->render('villes/create.html.twig');
    }
}