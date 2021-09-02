<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route ("/delete/id}", name="delete")
     */
    public function delete(Participant $participant,
                           EntityManagerInterface $entityManager): Response
    {

        if($participant->getImage()) {
            $img = $participant->getImage();
            $nomeImg=$img->getNom();

            if ($nomeImg=$img->getNom()) {
                unlink('../public/image/imagesProfil/' . $nomeImg);
            }
        }


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


    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifier(Request $request, Participant $participant): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $form = $this->createForm(ParticipantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()){

                if ($form->get('image')->getData()) {

                    $file = $form->get('image')->getData()->getFile();

                    if (!$file) {

                        $participant->setImage(null);
                    } else {
                        $nomImage = md5(uniqid()) . '.' . $participant->getPseudo() . '.' . $file->guessExtension();
                        $file->move('../public/image/imagesProfil', $nomImage);

                        $image = new Image();

                        $participant->getImage($image);
                        $image->setNom($nomImage);
                        $participant->setImage($image);
                    }
                }


                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Profil modifié !');
                return $this->redirectToRoute('participant_details', ['id' => $participant->getId()]
                );
            } else {
                $em->refresh($user);
            }
        }

        return $this->render('participant/modifier.html.twig', [
            'registrationForm' => $form->createView(),
            'user' => $user,
            'participant'=>$participant

        ]);
    }

}