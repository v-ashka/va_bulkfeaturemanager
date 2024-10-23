<?php

namespace Va_bulkfeaturemanager\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class BulkFeatureManagerRepository extends EntityRepository
{
    public function getAllIds(){
        $qb = $this
            ->createQueryBuilder('fm')
            ->select('fm.id_feature')
        ;

        return $qb->getQuery()->getScalarResult();
    }

}
