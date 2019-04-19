<?php

namespace App\Form;

use App\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre_page',null,[
                'required' =>  false,
                'attr' => [
                    'class' => 'form-control',
                ],

            ])
            ->add('color_titre_page', ColorType::class,[
                'required' =>  false,
                'attr' => [
                    'class' => 'form-control',

                ],

            ])
            ->add('bg_color', ColorType::class,[
                'required' =>  false,
                'attr' => [
                    'class' => 'form-control',
                    'value' => 	'#FFFFFF',

                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
