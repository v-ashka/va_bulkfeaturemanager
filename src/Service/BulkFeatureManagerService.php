<?php
declare(strict_types=1);

namespace Va_bulkfeaturemanager\Service;
use Doctrine\DBAL\Connection;
class BulkFeatureManagerService
{
    private $connection;
    private $dbPrefix;

    public function __construct(Connection $connection, string $dbPrefix)
    {
        $this->dbPrefix = $dbPrefix;
        $this->connection = $connection;
    }

    public function addFeaturesToProducts(int $featureId, int $featureValueId, array $productsId){
        $transactionLog = [];

        $this->connection->beginTransaction();
        try{
            foreach($productsId as $itemId){
//             before insert, check if featureId is not related already with productId
                $qbSelect = $this->connection->createQueryBuilder();

                $validFeature = $qbSelect
                    ->select('COUNT(1)')
                    ->from($this->dbPrefix . 'feature_product')
                    ->where("id_feature = :id_feature")
                    ->andWhere("id_product = :id_product")
                    ->setParameter(":id_feature", $featureId)
                    ->setParameter(':id_product',  $itemId)
                    ->execute()
                    ->fetchOne();

                if(!$validFeature){
                    $qbInsert = $this->connection->createQueryBuilder();

                    $qbInsert
                        ->insert($this->dbPrefix . 'feature_product')
                        ->values([
                            'id_feature' => '?',
                            'id_product' => '?',
                            'id_feature_value' => '?'
                        ])
                        ->setParameter(0, $featureId)
                        ->setParameter(1, $itemId)
                        ->setParameter(2, $featureValueId)
                        ->execute()
                    ;

                    $transactionLog['success'][] = $itemId;

                }else{
                    $transactionLog['warning'][] = $itemId;
                }
            }
            $this->connection->commit();
        } catch(\Exception $e){
            $this->connection->rollBack();
            throw $e;
        }
        return $transactionLog;
    }

    protected function removeFeatureFromProducts(){

    }

    protected function removeAllFeaturesFromProducts(){

    }

}
