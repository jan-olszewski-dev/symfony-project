<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\RegisterUserType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validation;

class RegisterUserTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->method('hashPassword')->willReturn(uniqid('password'));
        $testedType = new RegisterUserType($hasher);

        return [
            new ValidatorExtension($validator),
            new PreloadedExtension([$testedType], []),
        ];
    }

    /** @dataProvider validDataProvider */
    public function testSubmitValidData(
        string $email,
        string $plainPasswordFirst,
        string $plainPasswordSecond,
        string $firstName,
        string $lastName
    ): void {
        $user = new User();
        $form = $this->factory->create(RegisterUserType::class, $user);

        $form->submit([
            'email' => $email,
            'plainPassword' => [
                'first' => $plainPasswordFirst,
                'second' => $plainPasswordSecond,
            ],
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);

        $expectedUser = (new User())
            ->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($firstName, $user->getFirstName());
        $this->assertSame($lastName, $user->getLastName());
        $this->assertNotEmpty($user->getPassword());
        $this->assertEmpty($user->getPlainPassword());
    }

    /** @dataProvider invalidDataProvider */
    public function testSubmitInvalidData(
        ?string $email,
        ?string $plainPasswordFirst,
        ?string $plainPasswordSecond,
        ?string $firstName,
        ?string $lastName
    ): void {
        $form = $this->factory->create(RegisterUserType::class);

        $form->submit([
            'email' => $email,
            'plainPassword' => [
                'first' => $plainPasswordFirst,
                'second' => $plainPasswordSecond,
            ],
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);

        $this->assertFalse($form->isValid());
    }

    protected function validDataProvider(): array
    {
        return [
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                'zaq1@WSX',
                uniqid('firstName'),
                uniqid('lastName'),
            ],
        ];
    }

    /** @SuppressWarnings(PHPMD.ExcessiveMethodLength) */
    protected function invalidDataProvider(): array
    {
        return [
            [
                null,
                'zaq1@WSX',
                'zaq1@WSX',
                uniqid('firstName'),
                uniqid('lastName'),
            ],
            [
                '',
                'zaq1@WSX',
                'zaq1@WSX',
                uniqid('firstName'),
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                uniqid('first'),
                uniqid('second'),
                uniqid('firstName'),
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                null,
                'zaq1@WSX',
                uniqid('firstName'),
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                null,
                uniqid('firstName'),
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                '1234567',
                '1234567',
                uniqid('firstName'),
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                'zaq12WSX',
                'zaq12WSX',
                uniqid('firstName'),
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                'zaq1@WSX',
                null,
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                'zaq1@WSX',
                '',
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                'zaq1@WSX',
                'a',
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                'zaq1@WSX',
                substr(str_repeat(uniqid('firstName'), 50), 0, 51),
                uniqid('lastName'),
            ],
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                'zaq1@WSX',
                uniqid('firstName'),
                null,
            ],
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                'zaq1@WSX',
                uniqid('firstName'),
                '',
            ],
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                'zaq1@WSX',
                uniqid('firstName'),
                'a',
            ],
            [
                uniqid('email_').'@test.com',
                'zaq1@WSX',
                'zaq1@WSX',
                uniqid('firstName'),
                substr(str_repeat(uniqid('lastName'), 70), 0, 71),
            ],
        ];
    }
}
