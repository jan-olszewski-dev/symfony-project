<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserType extends AbstractType
{
    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\Email(),
                    new Assert\NotBlank(),
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'constraints' => [
                    new Assert\Length(min: 8),
                    new Assert\NotBlank(),
                    new Assert\Regex(
                        '/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W)/',
                        message: 'Password is too weak. Must contain at least one lower and upper case letters, digit and special character'
                    )
                ]
            ])
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Assert\Length(min: 2, max: 50),
                    new Assert\NotBlank(),
                ]
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Assert\Length(min: 3, max: 70),
                    new Assert\NotBlank(),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
