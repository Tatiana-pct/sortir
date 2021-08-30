<?php

namespace App\Controller;

use App\Entity\Ville;
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
        $ville =$villeRepository->findAll();
        $Ville = new Ville();
        $VilleForm =$this->createForm(VillesType::class,$Ville);
        $VilleForm->handleRequest($request);

        if($VilleForm->isSubmitted() && $VilleForm->isValid()) {
            $entityManager->persist($Ville);
            $entityManager->flush();

            $this->addFlash('succes','Ville ajoutée!');
            return $this->redirectToRoute('villes_liste');
        }



        return $this->render('villes/list.html.twig',[
            "ville" => $ville,
            'villeForm' => $VilleForm->createView()
        ]);

    }





    /**
     * @Route("/edit", name="edit")
     */
    public function editVille(int $id, EntityManagerInterface $entityManager)
    {
        $ville = new Ville();
        $form = $this-> createForm(VillesType::class, $ville);
        $form->remove('submit');
        $form->add('submit',SubmitType::class,[
            'label'=> 'modifier',
            'attr' => [
                'class'=> 'edit'
            ]
        ]);


        return $this->render('villes/create.html.twig');
    }


    /**
     * @Route("/delete", name="delete")
     */
    public function deleteVille(int $id, EntityManagerInterface $entityManager, Request $request)
    {
        $ville= $entityManager->getRepository(Ville::class)->find($request->get('id'));

        $entityManager->remove($ville);
        $entityManager->flush();
        return $this-> redirectToRoute('villes_');
    }
}