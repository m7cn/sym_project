<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
//            ->add('password')
            ->add('first_name')->setRequired(false)
            ->add('last_name')->setRequired(false)
            ->add('salutation',ChoiceType::class, [
                    'choices'  => [
                        '' => '',
                        'Ms' => 'Ms',
                        'Miss' => 'Miss',
                        'Mrs' => 'Mrs',
                        'Mr' => 'Mr',
                        'Dr' => 'Dr'
                    ],
                ]
            )
            ->add('phone',TelType::class)->setRequired(false)
            ->add('roles', ChoiceType::class, array(
                    'attr'  =>  array(
                                'class' => 'form-control',
                                'style' => 'margin:5px 0;'
                                ),
                    'choices' =>
                        array
                        (
                            'ROLE_ADMIN' => array
                            (
                                'Yes' => 'ROLE_ADMIN',
                            ),
                            'ROLE_USER' => array
                            (
                                'Yes' => 'ROLE_USER'
                            )
                        )
                ,
                    'multiple' => true,
                    'required' => true,
                )
            );
//            ->add('created_at')
//            ->add('updated_at')

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
