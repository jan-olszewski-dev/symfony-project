<?php

namespace App\Tests\Form;

use App\Entity\Address;
use App\Entity\City;
use App\Form\AddressType;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;


class AddressTypeTest extends TypeTestCase
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
        string  $street,
        string  $streetNumber,
        ?string $flatNumber,
        string  $postalCode
    ): void
    {

        $address = new Address();
        $form = $this->factory->create(AddressType::class, $address);

        $form->submit([
            'street' => $street,
            'streetNumber' => $streetNumber,
            'flatNumber' => $flatNumber,
            'postalCode' => $postalCode,
        ]);

        $expectedAddress = (new Address())
            ->setStreet($street)
            ->setStreetNumber($streetNumber)
            ->setFlatNumber($flatNumber)
            ->setPostalCode($postalCode);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expectedAddress, $address);
    }

    /** @dataProvider invalidDataProvider */
    public function testSubmitInvalidData(
        ?string $street,
        ?string $streetNumber,
        ?string $flatNumber,
        ?string $postalCode
    ): void
    {
        $form = $this->factory->create(AddressType::class, new Address());

        $form->submit([
            'street' => $street,
            'streetNumber' => $streetNumber,
            'flatNumber' => $flatNumber,
            'postalCode' => $postalCode,
        ]);

        $this->assertFalse($form->isValid());
    }

    protected function validDataProvider(): array
    {
        return [
            [
                uniqid('street'),
                (string)rand(0, 100),
                (string)rand(0, 1000),
                (string)rand(10000, 99999),
            ],
            [
                uniqid('street'),
                (string)rand(0, 100),
                null,
                (string)rand(10000, 99999),
            ],
        ];
    }

    /** @SuppressWarnings(PHPMD.ExcessiveMethodLength) */
    protected function invalidDataProvider(): array
    {
        return [
            [
                null,
                (string)rand(0, 100),
                (string)rand(0, 1000),
                (string)rand(10000, 99999),
            ],
            [
                substr(str_repeat(uniqid('street'), 81), 0, 81),
                (string)rand(0, 100),
                (string)rand(0, 1000),
                (string)rand(10000, 99999),
            ],
            [
                uniqid('street'),
                null,
                (string)rand(0, 1000),
                (string)rand(10000, 99999),
            ],
            [
                uniqid('street'),
                (string)rand(10000000000, 99999999999),
                (string)rand(0, 1000),
                (string)rand(10000, 99999),
            ],
            [
                uniqid('street'),
                (string)rand(0, 100),
                (string)rand(10000000000, 99999999999),
                (string)rand(10000, 99999),
            ],
            [
                uniqid('street'),
                (string)rand(0, 100),
                (string)rand(0, 1000),
                null,
            ],
            [
                uniqid('street'),
                (string)rand(0, 100),
                (string)rand(0, 1000),
                (string)rand(100000, 999999),
            ],
        ];
    }
}
