<?php

namespace App\Tests\Form;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class RestaurantTypeTest extends TypeTestCase
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
        string $name,
    ): void {
        $restaurant = new Restaurant();
        $form = $this->factory->create(RestaurantType::class, $restaurant);

        $form->submit([
            'name' => $name,
        ]);

        $expectedRestaurant = (new Restaurant())
            ->setName($name);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedRestaurant, $restaurant);
    }

    /** @dataProvider invalidDataProvider */
    public function testSubmitInvalidData(
        ?string $name,
    ): void {
        $form = $this->factory->create(RestaurantType::class, new Restaurant());

        $form->submit([
            'name' => $name,
        ]);

        $this->assertFalse($form->isValid());
    }

    protected function validDataProvider(): array
    {
        return [
            [
                uniqid('restaurant'),
            ],
        ];
    }

    /** @SuppressWarnings(PHPMD.ExcessiveMethodLength) */
    protected function invalidDataProvider(): array
    {
        return [
            [
                null,
            ],
            [
                substr(str_repeat(uniqid('restaurant'), 255), 0, 256),
            ],
        ];
    }
}
