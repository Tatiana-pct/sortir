<?php

namespace App\Form;

use App\data\RechercheData;
use App\Entity\Campus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
                      'label' => 'Campus',
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
                    'placeholder' => 'Rechercher'
                ]
            ])

            ->add('dateHeureDebut',DateType::class, [
                'label' => 'Entre le',
                'html5' => true,
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'datepicker'],

            ])

            ->add('dateCloture', DateType::class, [
                'label' => 'et le',
                'html5' => true,
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'datepicker'],
            ])

            ->add('organisateur', CheckboxType::class, [
                'label'=>'sorties dont je suis l\'organisateur/trice',
                'required' => false,
            ])

            ->add('inscrit', CheckboxType::class, [
                'label'=>'sorties auxquelles je suis inscrit/e',
                'required' => false,
            ])

            ->add('notInscrit', CheckboxType::class, [
                'label'=>'sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
            ])


            ->add('passee', CheckboxType::class, [
                'label'=>'Sorties passées',
                'required' => false,
            ])

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