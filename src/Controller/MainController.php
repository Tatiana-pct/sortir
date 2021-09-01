<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController

{
    /**
     * @Route("/", name="main_home")
     */
    public function home()
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('sortie_liste');
        } else {
            return $this->redirectToRoute('app_login');
        }
    }




}