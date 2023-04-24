<?php

namespace App\Repository;

use App\Entity\Premises;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Premises>
 *
 * @method Premises|null find($id, $lockMode = null, $lockVersion = null)
 * @method Premises|null findOneBy(array $criteria, array $orderBy = null)
 * @method Premises[]    findAll()
 * @method Premises[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PremisesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Premises::class);
    }

    public function save(Premises $entity): self
    {
        $this->getEntityManager()->persist($entity);

        return $this;
    }

    public function remove(Premises $entity): self
    {
        $this->getEntityManager()->remove($entity);

        return $this;
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
