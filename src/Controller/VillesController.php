<?php

namespace App\Controller;

use App\Entity\Ville;
use Symfony\Component\HttpFoundation\Request;
use App\Form\VillesType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function afficherVille(VilleRepository $villeRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $ville =$villeRepository->findAll();
        $Ville = new Ville();
        $VilleForm =$this->createForm(VillesType::class,$Ville);
        $VilleForm->handleRequest($request);

        if($VilleForm->isSubmitted() && $VilleForm->isValid()) {
            $entityManager->persist($Ville);
            $entityManager->flush();

            return $this->redirectToRoute('villes_liste');
        }



        return $this->render('villes/list.html.twig',[
            "ville" => $ville,
            'villeForm' => $VilleForm->createView()
        ]);

    }


    /**
     * @Route("/create", name="create")
     */
    public function createVille()
    {

        return $this->render('villes/list.html.twig');



    }


    /**
     * @Route("/edit", name="edit")
     */
    public function editVille(int $id, EntityManagerInterface $entityManager)
    {
        $ville = new ville();
        $ville->setNom('');

        return $this->render('villes/create.html.twig');
    }


    /**
     * @Route("/delete", name="delete")
     */
    public function deleteVille(int $id, EntityManagerInterface $entityManager)
    {
        $ville= new ville();

        $entityManager->remove($ville);
        $entityManager->flush();
        return $this->render('villes/create.html.twig');
    }
}