<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class DashboardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('passwordold', PasswordType::class, [
                'attr' => [
                    'class' => 'input100',
                    'id' => 'js-token-2'
                ],
                'mapped' => false,
                'required' => true,
                'label' => 'Your Actual Password',
            ])
            ->add('newpassword', RepeatedType::class, [
                'attr' => [
                    'class' => 'input100',
                    'id' => 'js-token-2'
                ],
                'type' => PasswordType::class,
                'first_name' => 'pass',
                'second_name' => 'confirm',
                'first_options'  => [
                    'label' => 'Password',
                    'attr' => [
                        'class' => 'input100',
                        'id' => 'js-token-2'
                    ],

                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'attr' => [
                        'class' => 'input100',
                        'id' => 'js-token-2'
                    ],
                ],
                'mapped' => false,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
