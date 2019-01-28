<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BussinesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('descriptionen', TextareaType::class)
            ->add('file', FileType::class, array('required' => false))
            ->add('address', TextType::class)
            ->add('price', NumberType::class)
            ->add('descuento', NumberType::class)
            ->add('pago', NumberType::class)
            ->add('email', TextType::class)
            ->add('website', TextType::class)
            ->add('country', TextType::class)
            ->add('estate', TextType::class)
            ->add('postalcode', TextType::class)
            ->add('facebook', TextType::class, array('required' => false))
            ->add('instagram', TextType::class, array('required' => false))
            ->add('categoryid', EntityType::class , array('class' => 'AdminBundle:Category'))
            ->add('userid', EntityType::class, array('class' => 'AdminBundle:User'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AdminBundle\Entity\Bussines'
        ));
    }
}
