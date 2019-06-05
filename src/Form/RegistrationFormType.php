<?php
/**
 * Created by PhpStorm.
 * User: samar
 * Date: 14/03/19
 * Time: 13:01
 */

namespace App\Form;


use phpDocumentor\Reflection\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numerotel', TelType::class)
            ->add('avatar', FileType::class, [
                'required' => false
            ]);

    }

    public function getParent()
    {
        return BaseRegistrationFormType::class;
    }


}