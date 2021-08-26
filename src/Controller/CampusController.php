<?php

namespace App\Controller;


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
    public function afficherVilles()
    {
        return $this->render('campus/list.html.twig');
    }


    /**
     * @Route("/create", name="create")
     */
    public function createVille()
    {
        return $this->render('campus/create.html.twig');
    }
}