<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        AppAuthenticator $authenticator,
        Sender $sender): Response
    {
        $participant = new Participant();

        if ($participant->getAdministrateur()==1) {
            $participant->setRoles(["ROLE_ADMIN"]);
        } else {
            $participant->setRoles(["ROLE_USER"]);
            $participant->setActif(1);
        }


        $form = $this->createForm(RegistrationFormType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant->setMotDePasse(
                $passwordEncoder->encodePassword(
                    $participant,
                    $form->get('motDePasse')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            //gestion image

            $file=$form->get('image')->getData()->getFile();
            $nomImage = md5(uniqid()). '.'.$participant->getPseudo(). '.' .$file->guessExtension();

            $file->move('../public/image/imagesProfil', $nomImage);

            $image= new Image();
            $image->setNom($nomImage);


            $participant->setImage($image);

            $entityManager->persist($participant);
            $entityManager->flush();
            // do anything else you need here, like send an email
            $sender->sendNewUserNotificationToAdmin($participant);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $participant,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
