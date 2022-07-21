<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Quel nom souhaitez vous donner à votre adresse?',
                'attr' => [
                    'placeholder' => 'Nommez votre adresse'
                ]
            ])

            ->add('firstname',TextType::class,[
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => 'Entrez votre prénom'
                ]
            ])
            ->add('lastname', TextType::class,[
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Entrez votre nom'
                ]
            ])
            ->add('company', TextType::class,[
                'label' => 'Votre compagnie',
                'required' => false,
                'attr' => [
                    'placeholder' => '(optionel) Entrez votre société'
                ]
            ])
            ->add('address', TextType::class,[
                'label' => 'Quel nom souhaitez vous donner à votre adresse?',
                'attr' => [
                    'placeholder' => '15e avenue Mokolo ...'
                ]
            ])
            ->add('postal', TextType::class,[
                'label' => 'Votre code postal',
                'attr' => [
                    'placeholder' => 'Entrez votre code postal'
                ]
            ])
            ->add('city', TextType::class,[
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Entrez votre ville de residence'
                ]
            ])
            ->add('country', CountryType::class,[
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Entrez votre pays de résidence',
                    'class' => 'form-control'
                ]
            ])
            ->add('phone', TelType::class,[
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre contact'
                ]
            ])
            ->add('submit',SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn-block btn-outline-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
