<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,[
                'disabled'=> true,
                'label' => 'Email'
            ])
            ->add('firstname', TextType::class,[
                'disabled'=> true,
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class,[
                'disabled'=> true,
                'label' => 'Nom'
            ])
            ->add('old_password', PasswordType::class,[
                'mapped' => false,
                'disabled'=> false,
                'label' => 'Mot de passe actuel',
                'attr' =>
                    ['placeholder' => 'Veuillez saisir votre mot de passe actuel']
            ])

            ->add('new_password', RepeatedType::class, [
                'mapped' => false,
                'label'=>'Password',
                'type' => PasswordType::class,
                'required' => true,

                'first_options' => ['label' => 'Nouveau mot de passe',
                    'attr' =>
                        ['placeholder' => 'Veuillez saisir votre nouveau mot de passe']
                ],

                'second_options' => ['label' => 'Confirmer',
                    'attr' =>
                        ['placeholder' => 'Confirmez votre nouveau mot de passe']
                ]
            ])

            ->add('submit', SubmitType::class, [
                'label'=>"Mettre à jour"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
