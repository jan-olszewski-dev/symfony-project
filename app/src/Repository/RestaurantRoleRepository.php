<?php

namespace App\Repository;

use App\Entity\RestaurantRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RestaurantRole>
 *
 * @method RestaurantRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantRole[]    findAll()
 * @method RestaurantRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RestaurantRole::class);
    }

    public function save(RestaurantRole $entity): self
    {
        $this->getEntityManager()->persist($entity);

        return $this;
    }

    public function remove(RestaurantRole $entity): self
    {
        $this->getEntityManager()->remove($entity);

        return $this;
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
