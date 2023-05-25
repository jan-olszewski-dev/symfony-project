<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserType extends AbstractType
{
    public function __construct(
        public UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
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
                    ),
                ],
            ])
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, function (PostSubmitEvent $event) {
                $form = $event->getForm();
                if (!$form->isValid() || !$form->has('plainPassword')) {
                    return;
                }

                /** @var User $user */
                $user = $form->getData();
                $hash = $this->userPasswordHasher->hashPassword($user, (string) $user->getPlainPassword());
                $user->setPassword($hash);
                $user->eraseCredentials();
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
