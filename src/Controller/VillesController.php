<?php

namespace App\Controller;

use App\data\RechercheData;
use App\Entity\Ville;
use App\Form\RechercheForm;
use App\Form\RechercheVilleFormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
        //affichage de la list des villes
        $ville =$villeRepository->findAll();
        $Ville = new Ville();
        $VilleForm =$this->createForm(VillesType::class,$Ville);
        $VilleForm->handleRequest($request);

        //validation du formulaire d'ajout d'une ville
        if($VilleForm->isSubmitted() && $VilleForm->isValid()) {
            $entityManager->persist($Ville);
            $entityManager->flush();

        //affichage d'un message lors du succes d'ajout d'une ville
            $this->addFlash('succes','Ville ajoutée!');

        //retirection de donne vers la page
            return $this->redirectToRoute('villes_liste');
        }

        //declaration du formulaire de recherche

        $rechercheVilleForm = $this->createForm(RechercheVilleFormType::class, $ville);
        $rechercheVilleForm->handleRequest($request);

        return $this->render('villes/list.html.twig',[
            "ville" => $ville,
            'villeForm' => $VilleForm->createView(),
            'rechercheVilleForm'=> $rechercheVilleForm-> createView()

        ]);



    }








    /**
     * @Route("/edit", name="edit")
     */
    public function editVille(int $id, EntityManagerInterface $entityManager,Request $request)
    {
        $ville = new Ville();
        $form = $this-> createForm(VillesType::class, $ville);
        $form->remove('submit');
        $form->add('submit',SubmitType::class,[
            'label'=> 'modifier'
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ville = $form->getData();

            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', 'La ville a bien été modifiée !');

            $this->villesListe = $entityManager->getRepository(Ville::class)->findAll();

            return $this->redirectToRoute('villes_liste');
        }

        return $this->render('villes/list.html.twig', [
            'page_name' => 'Modification de la ville',
            'ville' => $ville,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/delete", name="delete")
     */
    public function deleteVille(int $id, EntityManagerInterface $entityManager, Request $request)
    {
        $ville= $entityManager->getRepository(Ville::class)->find($request->get('id'));
        // TODO: faire methode de suppression des villes
        $entityManager->remove($ville);
        $entityManager->flush();
        return $this-> redirectToRoute('villes_');
    }
}