<?php

namespace App\Form;

use App\Entity\Campus;
use PhpParser\Node\Stmt\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreCampusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', ChoiceType::class, [
                'choices' => [
                    'SAINT-HERBLAIN' => 'SAINT-HERBLAIN',
                    'CHARTRES DE BRERAGNE' => 'CHARTRES DE BRERAGNE',
                    'LA ROCHE SUR YON' => 'LA ROCHE SUR YON'
                ],
                'multiple' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Campus::class,
        ]);
    }
}
