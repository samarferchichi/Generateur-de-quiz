<?php
/**
 * Created by PhpStorm.
 * User: samar
 * Date: 14/03/19
 * Time: 13:01
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numerotel',null);
    }

    public function getParent()
    {
        return BaseRegistrationFormType::class;
    }


}