<?php

namespace App\Tests\Form;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Premises;
use App\Form\PremisesType;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class PremisesTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getClassMetadata')->willReturn(new ClassMetadata(City::class));

        $execute = $this->createMock(AbstractQuery::class);
        $execute->method('execute')->willReturn([]);

        $query = $this->createMock(QueryBuilder::class);
        $query->method('getQuery')->willReturn($execute);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->method('createQueryBuilder')->willReturn($query);

        $entityManager->method('getRepository')->willReturn($entityRepository);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManagerForClass')->willReturn($entityManager);
        $registry->method('getManagers')->willReturn([$entityManager]);

        return [
            new DoctrineOrmExtension($registry),
            new ValidatorExtension($validator),
        ];
    }

    /** @dataProvider validDataProvider */
    public function testSubmitValidData(
        string $name,
        string $street,
        string $streetNumber,
        ?string $flatNumber,
        string $postalCode
    ): void {
        $premises = new Premises();
        $form = $this->factory->create(PremisesType::class, $premises);

        $form->submit([
            'name' => $name,
            'address' => [
                'street' => $street,
                'streetNumber' => $streetNumber,
                'flatNumber' => $flatNumber,
                'postalCode' => $postalCode,
            ],
        ]);

        $address = (new Address())
            ->setStreet($street)
            ->setStreetNumber($streetNumber)
            ->setFlatNumber($flatNumber)
            ->setPostalCode($postalCode);
        $expectedPremises = (new Premises())
            ->setName($name)
            ->setAddress($address);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedPremises, $premises);
    }

    /** @dataProvider invalidDataProvider */
    public function testSubmitInvalidData(
        ?string $name,
        ?string $street,
        ?string $streetNumber,
        ?string $flatNumber,
        ?string $postalCode
    ): void {
        $form = $this->factory->create(PremisesType::class, new Premises());

        $form->submit([
            'name' => $name,
            'address' => [
                'street' => $street,
                'streetNumber' => $streetNumber,
                'flatNumber' => $flatNumber,
                'postalCode' => $postalCode,
            ],
        ]);

        $this->assertFalse($form->isValid());
    }

    protected function validDataProvider(): array
    {
        return [
            [
                uniqid('name'),
                uniqid('street'),
                (string) rand(0, 100),
                (string) rand(0, 1000),
                (string) rand(10000, 99999),
            ],
            [
                uniqid('name'),
                uniqid('street'),
                (string) rand(0, 100),
                null,
                (string) rand(10000, 99999),
            ],
        ];
    }

    /** @SuppressWarnings(PHPMD.ExcessiveMethodLength) */
    protected function invalidDataProvider(): array
    {
        return [
            [
                null,
                uniqid('street'),
                (string) rand(0, 100),
                (string) rand(0, 1000),
                (string) rand(10000, 99999),
            ],
            [
                substr(str_repeat(uniqid('name'), 255), 0, 256),
                uniqid('street'),
                (string) rand(0, 100),
                (string) rand(0, 1000),
                (string) rand(10000, 99999),
            ],
            [
                uniqid('name'),
                null,
                (string) rand(0, 100),
                (string) rand(0, 1000),
                (string) rand(10000, 99999),
            ],
            [
                uniqid('name'),
                substr(str_repeat(uniqid('street'), 81), 0, 81),
                (string) rand(0, 100),
                (string) rand(0, 1000),
                (string) rand(10000, 99999),
            ],
            [
                uniqid('name'),
                uniqid('street'),
                null,
                (string) rand(0, 1000),
                (string) rand(10000, 99999),
            ],
            [
                uniqid('name'),
                uniqid('street'),
                (string) rand(10000000000, 99999999999),
                (string) rand(0, 1000),
                (string) rand(10000, 99999),
            ],
            [
                uniqid('name'),
                uniqid('street'),
                (string) rand(0, 100),
                (string) rand(10000000000, 99999999999),
                (string) rand(10000, 99999),
            ],
            [
                uniqid('name'),
                uniqid('street'),
                (string) rand(0, 100),
                (string) rand(0, 1000),
                null,
            ],
            [
                uniqid('name'),
                uniqid('street'),
                (string) rand(0, 100),
                (string) rand(0, 1000),
                (string) rand(100000, 999999),
            ],
        ];
    }
}
