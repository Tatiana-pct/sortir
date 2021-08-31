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


    /**
     * @Route("/modifier/{id}", name="modifier")
     */
    public function modifier(Request $request, Participant $participant): Response
    {
        $em = $this->getDoctrine()->getManager();
        //récupère le user en session
        //ne jamais récupérer le user en fonction de l'id dans l'URL !
        $user = $this->getUser();

        $form = $this->createForm(ParticipantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //séparé en 2 if pour pouvoir faire le refresh si le form n'est pas valide
            if ($form->isValid()){


                $file=$form->get('image')->getData()->getFile();
                $nomImage = md5(uniqid()). '.'.$participant->getPseudo(). '.' .$file->guessExtension();

                $file->move('../public/image/imagesProfil', $nomImage);

                $image= new Image();
                $image->setNom($nomImage);


                $participant->setImage($image);
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Profil modifié !');
                return $this->redirectToRoute('participant_details', ['id' => $participant->getId()]
                );
            }
            else {
                $em->refresh($user);
            }
        }

        return $this->render('participant/modifier.html.twig', [
            'registrationForm' => $form->createView(),
            'user' => $user,
            'participant'=>$participant

        ]);
    }

//    /**
//     * Modification du profil
//     *
//     * @Route("/modification/mot-de-passe", name="user_edit_password")
//     */
//    public function editPassword(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): Response
//    {
//        //récupère le user en session
//        //ne jamais récupérer le user en fonction de l'id dans l'URL !
//        /** @var User $user */
//        $user = $this->getUser();
//
//        $form = $this->createForm(EditPasswordType::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $hash = $passwordEncoder->encodePassword($user, $form->get('new_password')->getData());
//            $user->setPassword($hash);
//
//            $entityManager->persist($user);
//            $entityManager->flush();
//
//            $this->addFlash('success', 'Mot de passe modifié !');
//            //sinon ça bugue dans la session, ça me déconnecte
//            //refresh() permet de re-récupérer les données fraîches depuis la bdd
//            $entityManager->refresh($user);
//
//            return $this->redirectToRoute("user_profile", ["id" => $user->getId()]);
//        }
//
//        return $this->render('user/edit_password.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }


}