<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
            ->add('email')
            ->add('motDePasse', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                                     'message' => 'Please enter a password',
                                 ]),
                    new Length([
                                   'min' => 6,
                                   'minMessage' => 'Your password should be at least {{ limit }} characters',
                                   // max length allowed by Symfony for security reasons
                                   'max' => 4096,
                               ]),
                ],
            ])
            ->add('campus',
                  EntityType::class, [
                      'class' => Campus::class,
                      'choice_label' => 'nom',
                      'query_builder' => function(EntityRepository $repository) {
                          return $repository->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                      }])

            ->add('administrateur')

            ->add('actif')

            ->add('image', ImageType::class, [
                'label'=>false,
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer'
            ])

            ->add('annuler', ResetType::class, [
                'label' => 'Annuler',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                                   'data_class' => Participant::class,
                               ]);
    }
}