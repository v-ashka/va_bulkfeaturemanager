<?php
declare(strict_types=1);

namespace Va_bulkfeaturemanager\Service;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

class BulkFeatureManagerService
{
    private $connection;
    private $dbPrefix;

    public function __construct(Connection $connection, string $dbPrefix)
    {
        $this->dbPrefix = $dbPrefix;
        $this->connection = $connection;
    }

    private function findExisitingFeature($qbSelect, $featureId, $itemId){
        $validData = $qbSelect
            ->select('COUNT(1)')
            ->from($this->dbPrefix . 'feature_product')
            ->where("id_feature = :id_feature")
            ->andWhere("id_product = :id_product")
            ->setParameter(":id_feature", $featureId)
            ->setParameter(':id_product',  $itemId)
            ->execute()
            ->fetchOne();

        return $validData;
    }

    public function addFeaturesToProducts(int $featureId, int $featureValueId, array $productsId){
        $transactionLog = [];

        $this->connection->beginTransaction();
        try{
            foreach($productsId as $itemId){
//             before insert, check if featureId is not related already with productId
                $qbSelect = $this->connection->createQueryBuilder();
                $validFeature = $this->findExisitingFeature($qbSelect, $featureId, $itemId);

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

    public function removeFeatureFromProducts(int $featureId, array $productsId){
        $transactionLog = [];
        $this->connection->beginTransaction();
        try{


            foreach($productsId as $itemId){
                $qbDelete = $this->connection->createQueryBuilder();
                $validFeature = $this->findExisitingFeature($qbDelete, $featureId, $itemId);
                if($validFeature){
                    $qbDelete
                        ->delete($this->dbPrefix . 'feature_product')
                        ->where('id_product = :id_product')
                        ->andWhere('id_feature = :id_feature')
                        ->setParameter(":id_product", $itemId)
                        ->setParameter(':id_feature', $featureId)
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

    public function removeAllFeaturesFromProducts(array $productsId){
        $transactionLog = [];
        $this->connection->beginTransaction();
        try{
            foreach($productsId as $itemId){
                $qbDelete = $this->connection->createQueryBuilder();
                $qbDelete
                    ->delete($this->dbPrefix . 'feature_product')
                    ->where('id_product = :id_product')
                    ->setParameter(":id_product", $itemId)
                    ->execute()
                ;
                $transactionLog['success'][] = $itemId;
            }
            $this->connection->commit();
        } catch(\Exception $e){
            $this->connection->rollBack();
            throw $e;
        }

        return $transactionLog;
    }

    /**
     * @throws Exception
     */
    public function getFeatureByProductId(int $productId){

        $qbSelectId = $this->connection->createQueryBuilder();
        return $qbSelectId
                ->select('fid.id_feature, fid.id_feature_value, fl.name, fvl.value')
                ->from($this->dbPrefix . 'feature_product', 'fid')
                ->where('id_product = :id_product')
                ->setParameter(':id_product', $productId)
            ->innerJoin('fid', $this->dbPrefix . 'feature_lang', 'fl', 'fid.id_feature = fl.id_feature')
            ->innerJoin('fid', $this->dbPrefix . 'feature_value_lang', 'fvl', 'fvl.id_feature_value = fid.id_feature_value')
                ->execute()
            ->fetchAllAssociative()
                ;

    }

}
