<?php

namespace App\Form;

use App\data\RechercheData;
use App\Entity\Campus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class rechercheSortieForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus',
                  EntityType::class, [
                      'class' => Campus::class,
                      'choice_label' => 'nom',
                      'required' => false,
                      'query_builder' => function(EntityRepository $repository) {
                          return $repository->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                      }])
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher',
                    ''
                ]
            ])

            //TODO: recherche avec Date et options

        ;

    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                                   'data_class'=>RechercheData::class,
                                   'method'=>'GET',
                                   'csrf_protection'=>false
                               ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}