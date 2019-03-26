<?php

namespace App\Form;

use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'required' =>  false,
                'label' => 'Titre de votre quiz',
                'label_attr' => [
                    'class' => ''
                ]
            ])
            ->add('color_titre', ColorType::class,[
                'required' =>  false,

            ])
            ->add('police', ChoiceType::class, [
                'choices' => [
                    'Open sans conseillée' =>  'Open sans conseillée',
                    'Time New roman' => 1,

                    ],
                'required' =>false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('gras')
            ->add('italique')
            ->add('image', null, [
                'required' =>  false,
                'label'=> 'Image/Logo à faire apparaitre en debut de votre quiz'
            ])
            ->add('description',TextareaType::class, [
                'attr' =>
                    [
                        'class' => 'form-control',
                        ],

                'required' =>  false,
                'label' => 'Description de votre quiz',

            ])
            ->add('mode_correction',null, [

                'label'=> 'Activer le mode correction sur votre quiz: la bonne réponse sera affichée aux répondants aprés qu\'ils aient réponduus'
            ])
            ->add('mode_score', null,[
                'attr' => [ 'custom-control-input' ],
                'label' => ' Activer le mode score sur votre quiz :le scrore des répondants sera calculé en fonction de leurs réponses'
            ])
            ->add('temps_dispo',null,[
                'label' => 'Temps disponible'
            ])
            ->add('nb_question', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    'illimité'=>'1000'
                ],

                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nombres des questions par pages',

            ])
            ->add('nb_page', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    'illimité'=>'100'
                ],
                'attr' => [
                    'class' => 'form-control'
                ],

                'label' => 'Nombres des pages ',

            ])
            ->add('melange_question',null,[
                'required' =>  false,
                'label' => 'Melanger les questions'

            ])

            ->add('ouvrire_quiz')
            ->add('fermer_quiz')

            ->add('send_mail',null,[
                'required' =>  false,
                'label' => 'Envoyer le quiz par email'

            ])
            ->add('imprime_pdf',null,[
                'required' =>  false,
                'label' => 'Imprimer le quiz en pdf'

            ])


            ->add('entete',TextareaType::class, [
                'attr' =>
                    [
                        'class' => 'form-control',
                    ],

                'required' =>  false,
                'label' => 'Entéte de votre quiz :',

            ])
            ->add('pied',TextareaType::class, [
                'attr' =>
                    [
                        'class' => 'form-control',
                    ],

                'required' =>  false,
                'label' => 'Pied de votre quiz :',

            ])


            ->add('message_s',TextareaType::class, [
                'attr' =>
                    [
                        'class' => 'form-control',
                    ],

                'required' =>  false,
                'label' => 'Message de succés :',

            ])
            ->add('message_e',TextareaType::class, [
                'attr' =>
                    [
                        'class' => 'form-control',
                    ],

                'required' =>  false,
                'label' => 'Message d\'erreur :',

            ])


            ->add('nb_tentative', ChoiceType::class, [
                'choices' => [
                    'Nombre illimité de tentatives'=>100,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10,
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nombres des tentatives autorisées',

            ])
            ->add('brochure', FileType::class, ['data_class' => null
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
