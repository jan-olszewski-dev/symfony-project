<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\RegisterUserType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\Traits\ValidatorExtensionTrait;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class RegisterUserTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    /** @dataProvider validDataProvider */
    public function testSubmitValidData(RegisterUserDTO $registerUserDTO)
    {
        $user = new User();
        $form = $this->factory->create(RegisterUserType::class, $user);

        $form->submit([
            'email' => $registerUserDTO->getEmail(),
            'plainPassword' => [
                'first' => $registerUserDTO->getPlainPasswordFirst(),
                'second' => $registerUserDTO->getPlainPasswordSecond()
            ],
            'firstName' => $registerUserDTO->getFirstName(),
            'lastName' => $registerUserDTO->getLastName(),
        ]);

        $expectedUser = (new User())
            ->setEmail($registerUserDTO->getEmail())
            ->setPlainPassword($registerUserDTO->getPlainPasswordFirst())
            ->setFirstName($registerUserDTO->getFirstName())
            ->setLastName($registerUserDTO->getLastName());

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedUser, $user);
    }

    /** @dataProvider invalidDataProvider */
    public function testSubmitInvalidData(RegisterUserDTO $registerUserDTO)
    {
        $form = $this->factory->create(RegisterUserType::class);

        $form->submit([
            'email' => $registerUserDTO->getEmail(),
            'plainPassword' => [
                'first' => $registerUserDTO->getPlainPasswordFirst(),
                'second' => $registerUserDTO->getPlainPasswordSecond()
            ],
            'firstName' => $registerUserDTO->getFirstName(),
            'lastName' => $registerUserDTO->getLastName(),
        ]);

        $this->assertFalse($form->isValid());
    }

    protected function validDataProvider(): array
    {
        return [
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    uniqid('firstName'),
                    uniqid('lastName')
                )
            ],
        ];
    }

    protected function invalidDataProvider(): array
    {
        return [
            [
                new RegisterUserDTO(
                    null,
                    'zaq1@WSX',
                    'zaq1@WSX',
                    uniqid('firstName'),
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    '',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    uniqid('firstName'),
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    uniqid('first'),
                    uniqid('second'),
                    uniqid('firstName'),
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    null,
                    'zaq1@WSX',
                    uniqid('firstName'),
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    null,
                    uniqid('firstName'),
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    '1234567',
                    '1234567',
                    uniqid('firstName'),
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq12WSX',
                    'zaq12WSX',
                    uniqid('firstName'),
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    null,
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    '',
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    'a',
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    substr(str_repeat(uniqid('firstName'), 50), 0, 51),
                    uniqid('lastName')
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    uniqid('firstName'),
                    null
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    uniqid('firstName'),
                    ''
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    uniqid('firstName'),
                    'a'
                )
            ],
            [
                new RegisterUserDTO(
                    uniqid('email_') . '@test.com',
                    'zaq1@WSX',
                    'zaq1@WSX',
                    uniqid('firstName'),
                    substr(str_repeat(uniqid('lastName'), 70), 0, 71)
                )
            ],
        ];
    }
}

final class RegisterUserDTO
{
    public function __construct(
        private ?string $email,
        private ?string $plainPasswordFirst,
        private ?string $plainPasswordSecond,
        private ?string $firstName,
        private ?string $lastName
    )
    {
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPlainPasswordFirst(): ?string
    {
        return $this->plainPasswordFirst;
    }

    public function getPlainPasswordSecond(): ?string
    {
        return $this->plainPasswordSecond;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
