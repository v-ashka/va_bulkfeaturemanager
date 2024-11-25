<?php

namespace Va_bulkfeaturemanager\Repository;

use Va_bulkfeaturemanager\Entity\UnitFeature;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @method UnitFeature|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnitFeature|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnitFeature[] findAll()
 * @method UnitFeature[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitFeatureRepository extends EntityRepository
{
}
