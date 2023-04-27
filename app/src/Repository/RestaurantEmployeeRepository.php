<?php

namespace App\Repository;

use App\Entity\RestaurantEmployee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RestaurantEmployee>
 *
 * @method RestaurantEmployee|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantEmployee|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantEmployee[]    findAll()
 * @method RestaurantEmployee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantEmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantEmployee::class);
    }

    public function save(RestaurantEmployee $entity): self
    {
        $this->getEntityManager()->persist($entity);

        return $this;
    }

    public function remove(RestaurantEmployee $entity): self
    {
        $this->getEntityManager()->remove($entity);

        return $this;
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
