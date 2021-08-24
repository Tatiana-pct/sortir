<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/profil")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_profile", requirements={"id": "\d+"})
     */
    public function profil()
    {

        return $this->render('user/profil.html.twig');
    }

}