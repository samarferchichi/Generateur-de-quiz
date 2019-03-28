<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text_question', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' =>  false,
                'label' => 'Titre de votre quiz',
                'label_attr' => [
                    'class' => ''
                ]
            ])
            ->add('type_question', ChoiceType::class, [

                'choices' => [
                    'Réponse courte' => 'Réponse courte',
                    'Vrai/faux' => 'Vrai/faux',
                    'Case à cocher'=>'Case à cocher',
                    'Liste déroulante'=> 'Liste déroulante',
                    'Calculée'=>'Calculée'

                ],

                'required' =>  false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Type question',

            ])
            ->add('info_bulle', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' =>  false,
                'label' => 'Info bulle :',
                'label_attr' => [
                    'class' => ''
                ]
            ])
            ->add('description_question',TextareaType::class, [
                'attr' =>
                    [
                        'class' => 'form-control',
                    ],

                'required' =>  false,
                'label' => 'Description de question :',

            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
