<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',
                  null,
                  [
                      'label'=>'Nom de la sortie'
                  ])

            ->add('dateHeureDebut',
                  DateTimeType::class,
                  [
                      'label'=>'Date et heure de la sortie',
                      'html5' => true,
                      'widget' => 'single_text',
                      'years' => range(2021,2022),
                  ])

            ->add('duree',
                  IntegerType::class,
                  [
                      'label' => 'Durée'
                  ])

            ->add('dateLimiteInscription',
                  DateType::class,
                  [
                      'widget' => 'single_text',
                      // 'format' => 'dd/MM/yyyy HH:mm',
                      'html5' => true,
                      'label'=>'Date limite d\'inscription'
                  ])

            ->add('nbInscriptionsMax',
                  IntegerType::class,
                  [
                      'label' => 'Nombre de place',
                      'attr' => [
                          'min' => '1',
                          'max' => '100']
                  ])

            ->add('infosSortie' ,
                  TextareaType::class,
                  [
                      'label' => "Description et infos"
                  ])

            ->add('Lieu',
                  EntityType::class,
                  [
                      'label' => 'Lieu',
                      'class' => Lieu::class,
                      'choice_label' => 'nom',
                  ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                                   'data_class' => Sortie::class,
                               ]);
    }
}
