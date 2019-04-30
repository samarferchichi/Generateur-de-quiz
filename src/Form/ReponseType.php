<?php

namespace App\Form;

use App\Entity\Reponse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('formule',null,[
                'label' => 'Formule :'
            ])
            ->add('destext')
            ->add('desnumber')
            ->add('resultatformule',null,[
                'label' => 'Resultat :'
            ])
            ->add('descriptiondate',null,[
                    'label' => 'Description date :'
            ]
)
            ->add('descriptionformule',null,[
                'label' => 'Description formule :'
            ])
            ->add('reponse_valide')
            ->add('etatvf')
            ->add('etatlist')
            ->add('etatcaseacocher')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}
