<?php

namespace Va_bulkfeaturemanager\Repository;

use Doctrine\ORM\EntityRepository;

class BulkFeatureManagerRepository
{
    public function getAllIds(){
        $qb = $this
            ->createQueryBuilder('fm')
            ->select('fm.id_feature')
        ;

        return $qb->getQuery()->getScalarResult();
    }
}
