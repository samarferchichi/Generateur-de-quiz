<?php

namespace App\Form;

use App\Entity\Quiz;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('color_titre')
            ->add('police')
            ->add('gras')
            ->add('italique')
            ->add('image')
            ->add('description')
            ->add('mode_correction')
            ->add('mode_score')
            ->add('temps_dispo')
            ->add('nb_question')
            ->add('nb_page')
            ->add('melange_question')
            ->add('ouvrire_quiz')
            ->add('fermer_quiz')
            ->add('send_mail')
            ->add('imprime_pdf')
            ->add('entete')
            ->add('pied')
            ->add('message_s')
            ->add('message_e')
            ->add('nb_tentative')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
