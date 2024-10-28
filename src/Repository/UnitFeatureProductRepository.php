<?php

namespace Va_bulkfeaturemanager\Repository;

use Va_bulkfeaturemanager\Entity\UnitFeatureProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UnitFeatureProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnitFeatureProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnitFeatureProduct[] findAll()
 * @method UnitFeatureProduct[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitFeatureProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnitFeatureProduct::class);
    }
}
