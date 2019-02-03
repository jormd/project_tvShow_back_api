<?php
/**
 * Created by PhpStorm.
 * User: romandjohann
 * Date: 2019-02-02
 * Time: 13:49
 */

namespace App\form\type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationPersoFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('name', null)
            ->add('lastname', null)
            ->add('password', null)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'registration';
    }


}