<?php

namespace App\Form;

use App\Entity\Quiz;
use Doctrine\DBAL\Types\DateType;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\File;
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
            ->add('gras', null, [
                'attr' => [
                    'data-toggle' => "toggle",
                    'data-onstyle' => "info",
                    'data-on' => "Oui",
                    'data-off' => "Non"
                ],
                'data' => true
            ])
            ->add('italique', null, [
                'attr' => [
                    'data-toggle' => "toggle",
                    'data-onstyle' => "info",
                    'data-on' => "Oui",
                    'data-off' => "Non"
                ],
                'data' => true
            ])

            ->add('description',TextareaType::class, [
                'attr' =>
                    [
                        'class' => 'form-control',
                        ],

                'required' =>  false,
                'label' => 'Description de quiz',


            ])
            ->add('mode_correction',null, [
                'label'=> 'Activer le mode correction sur votre quiz: la bonne réponse sera affichée aux répondants aprés qu\'ils aient réponduus.
                Activer le mode score sur votre quiz :le scrore des répondants sera calculé en fonction de leurs réponses',
                'label_attr' => [
                    'style' => 'margin-left: 17px;'
                ],
                'attr' => [
                    'data-toggle' => "toggle",
                    'data-onstyle' => "success",
                    'data-on' => "Oui",
                    'data-off' => "Non"
                ],
                'data' => true
            ])

            ->add('temps_dispo',null,[
                'label' => 'Temps disponible',

                'attr' => [
                    'data-toggle' => "toggle",
                    'data-onstyle' => "warning",
                    'data-on' => "Oui",
                    'data-off' => "Non"
                ],
                'data' => true
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
                'label_attr' => [
                    'style' => 'margin-left: 16px;'
                ]

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
                'label_attr' => [
                    'style' => 'margin-left: 5px;'
                ]

            ])
            ->add('melange_question',null,[
                'required' =>  false,
                'label' => 'Melanger les questions',
                'attr' => [
                    'data-toggle' => "toggle",
                    'data-onstyle' => "info",
                    'data-on' => "Oui",
                    'data-off' => "Non"
                ],
                'data' => true

            ])

            ->add('ouvrire_quiz', TextType::class,[
                'required' =>false,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => "off",
                    'width' => '295px'
                ]
            ])
            ->add('fermer_quiz',TextType::class,[
                'required' =>false,
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => "off",
                    'width' => '295px'

                ],

            ])

            ->add('send_mail',null,[
                'required' =>  false,
                'label' => 'Envoyer le quiz par email',
                'attr' => [
                    'data-toggle' => "toggle",
                    'data-onstyle' => "info",
                    'data-on' => "Oui",
                    'data-off' => "Non"
                ],
                'data' => true

            ])
            ->add('imprime_pdf',null,[
                'required' =>  false,
                'label' => 'Imprimer le quiz en pdf',
                'attr' => [
                    'data-toggle' => "toggle",
                    'data-onstyle' => "info",
                    'data-on' => "Oui",
                    'data-off' => "Non"
                ],
                'data' => true

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
                'label_attr' => [
                    'style' => 'margin-left: 16px;'
                ]

            ])
            ->add('brochure',FileType::class,[
                'required' =>  false,
                'data_class' => null,
                'label'=> 'Image/Logo à faire apparaitre en debut de votre quiz',



            ])



            ->add('Categorie',ChoiceType::class,[
                'choices' => [
                    'Autre' => "Autre",
                    'Education' => "Education",
                    'Santé' => "Santé",
                    'Ressources Humaines' => "Ressources Humaines",
                    'Evenementiel' => "Evenementiel",
                   ],
                'label' => 'Categorie',
                'attr' => [
                    'class' => 'form-control',
                ],
                'label_attr' => [
                    'style' => 'margin-left: 16px;'
                ]

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
