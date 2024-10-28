<?php

namespace Va_bulkfeaturemanager\Repository;

use Va_bulkfeaturemanager\Entity\UnitFeatureValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UnitFeatureValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnitFeatureValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnitFeatureValue[] findAll()
 * @method UnitFeatureValue[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitFeatureValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnitFeatureValue::class);
    }
}
