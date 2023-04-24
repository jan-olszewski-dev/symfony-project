<?php

namespace App\Repository;

use App\Entity\Disposition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Disposition>
 *
 * @method Disposition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Disposition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Disposition[]    findAll()
 * @method Disposition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DispositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Disposition::class);
    }

    public function save(Disposition $entity): self
    {
        $this->getEntityManager()->persist($entity);

        return $this;
    }

    public function remove(Disposition $entity): self
    {
        $this->getEntityManager()->remove($entity);

        return $this;
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
