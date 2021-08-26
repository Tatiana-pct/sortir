<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/profil")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/", name="participant_profile")
     */
    public function profil()
    {

        return $this->render('participant/profil.html.twig');
    }

    public function create()
    {

    }
}