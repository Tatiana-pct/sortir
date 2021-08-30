<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @Route ("/participant", name="participant_")
 */
class ParticipantController extends AbstractController
{

    /**
     * @Route("/liste", name="list")
     */
    public function list(ParticipantRepository $participantRepository): Response
    {
        //aller chercher les series en BDD

        $participants = $participantRepository->findAll();

        return $this->render('Participant/liste.html.twig', ["participants" => $participants]);
    }


    /**
     * @Route ("/delete/{id}", name="delete")
     */
    public function delete(Participant $participant,
                           EntityManagerInterface $entityManager): Response
    {

        $img = $participant->getImage();
        $nomeImg=$img->getNom();

        unlink('../public/image/imagesProfil/'.$nomeImg);

        $entityManager->remove($participant);
        $entityManager->flush();

        return $this->redirectToRoute('main_home');
    }


    /**
     * @Route("/details/{id}", name="details")
     */
    public function details(int $id,
                            ParticipantRepository $participantRepository,
                            UserInterface $user): Response
    {
        $user=$this->getUser();
        $participant = $participantRepository->find($id);
        if (!$participant) {
            throw $this->createNotFoundException('!');
        }

        return $this->render('participant/details.html.twig', ["participant" => $participant,
            "user"=>$user]);
    }

}