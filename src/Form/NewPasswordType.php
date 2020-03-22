<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class NewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plain_password',RepeatedType::class, [
                  'mapped' => false,
                  'type' => PasswordType::class,
                  'invalid_message' => 'Les mots de passe ne correspondent pas.',
                  'options' => ['attr' => ['class' => 'password-field']],
                  'required' => true,
                  'constraints' => new Length(['min' => 8]),
                  'first_options'  => ['label' => 'Mot de passe'],
                  'second_options' => ['label' => 'Confirmation Mot de Passe'],
            ])
            ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
