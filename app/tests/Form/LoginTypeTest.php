<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegisterUserType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class LoginTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
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
    public function testSubmitValidData(
        string $email,
        string $password,
    ): void
    {
        $user = new User();
        $form = $this->factory->create(LoginType::class, $user);

        $form->submit([
            'email' => $email,
            'password' => $password,
        ]);

        $expectedUser = (new User())
            ->setEmail($email)
            ->setPassword($password);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedUser, $user);
    }

    /** @dataProvider invalidDataProvider */
    public function testSubmitInvalidData(
        ?string $email,
        ?string $password,
    ): void
    {
        $form = $this->factory->create(LoginType::class, new User());

        $form->submit([
            'email' => $email,
            'password' => $password,
        ]);

        $this->assertFalse($form->isValid());
    }

    protected function validDataProvider(): array
    {
        return [
            [
                uniqid('email_') . '@test.com',
                'zaq1@WSX',
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
            ],
            [
                '',
                'zaq1@WSX',
            ],
            [
                uniqid('email_') . '@test.com',
                null,
            ],
        ];
    }
}
