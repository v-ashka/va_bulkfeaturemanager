<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Repository;

//use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class BulkFeatureManagerRepository{

    private $connection;
    public function __construct(Connection $connection){
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function addFeaturesToProducts(array $productsIds, int $featureId, int $featureValId){
        $qb = $this->connection->createQueryBuilder();
        foreach($productsIds as $productId){
            $qb
                ->insert('ps_feature_product')
                ->values([
                    'id_feature' => ':id_feature',
                    'id_product' => ':id_product',
                    'id_feature_value' => ':id_feature_value',
                ])
                ->setParameters([
                    'id_feature' => $featureId,
                    'id_product' => $productId,
                    'id_feature_value' => $featureValId,
                ])
                ->execute()
            ;
        }
    }
}
