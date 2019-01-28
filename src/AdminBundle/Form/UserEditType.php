<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, array('required' => false))
            ->add('role', ChoiceType::class, array(
                'choices'  => array(
                    'Usuario' => 'ROLE_USER',
                    'Administrador' => 'ROLE_ADMIN',
                )))
            ->add('name', TextType::class)
            ->add('lastname', TextType::class)
            ->add('country', TextType::class)
            ->add('estate', TextType::class)
            ->add('postalcode', TextType::class)
            ->add('phonenumber', TextType::class)
            ->add('email', TextType::class)
            ->add('active', ChoiceType::class, array(
                'choices'  => array(
                    'Si' => '1',
                    'No' => '0',
                )))
            ->add('paypal', TextType::class)
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\User'
        ));
    }
}
