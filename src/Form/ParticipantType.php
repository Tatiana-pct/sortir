<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Email :'])
            ->add('prenom', null, ['label' => 'Prénom :'])
            ->add('nom', null, ['label' => 'Nom :'])
            ->add('telephone', null, ['label' => 'Téléphone :'])
            ->add('campus', EntityType::class, [
                'label' => 'Votre Campus',
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('image', ImageType::class, [
                'required'=> false,
                'label'=>false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'OK'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                                   'data_class' => Participant::class,
                               ]);
    }
}
