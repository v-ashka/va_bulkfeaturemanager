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
            ->from($this->dbPrefix . 'unit_feature_product')
//            ->where("id_unit_feature = :id_unit_feature")
            ->andWhere("id_product = :id_product")
//            ->setParameter(":id_unit_feature", $featureId)
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
                $qbAction = $this->connection->createQueryBuilder();

                    !$validFeature ? (
                        $qbAction->insert($this->dbPrefix . 'unit_feature_product')
                    ):  (
                        $qbAction->update($this->dbPrefix . 'unit_feature_product')
                    );
                    $qbAction
                        ->set('id_unit_feature', ':id_unit_feature')
                        ->set('id_product', ':id_product')
                        ->set('id_unit_feature_value', ':id_unit_feature_value');
                    if($validFeature)
                        $qbAction->where('id_product = :id_product');
                    $qbAction
                        ->setParameter(':id_unit_feature', $featureId)
                        ->setParameter(':id_product', $itemId)
                        ->setParameter(':id_unit_feature_value', $featureValueId);
                    $qbAction->execute();

                    !$validFeature  ? ($transactionLog['success'][] = $itemId):($transactionLog['warning'][] = $itemId);
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
                        ->delete($this->dbPrefix . 'unit_feature_product')
                        ->where('id_product = :id_product')
                        ->setParameter(":id_product", $itemId)
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
                    ->delete($this->dbPrefix . 'unit_feature_product')
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
//    public function getFeatureByProductId(int $productId){
//
//        $qbSelectId = $this->connection->createQueryBuilder();
//        return $qbSelectId
//                ->select('fid.id_feature, fid.id_feature_value, fl.name, fvl.value')
//                ->from($this->dbPrefix . 'feature_product', 'fid')
//                ->where('id_product = :id_product')
//                ->setParameter(':id_product', $productId)
//            ->innerJoin('fid', $this->dbPrefix . 'feature_lang', 'fl', 'fid.id_feature = fl.id_feature')
//            ->innerJoin('fid', $this->dbPrefix . 'feature_value_lang', 'fvl', 'fvl.id_feature_value = fid.id_feature_value')
//                ->execute()
//            ->fetchAllAssociative()
//                ;
//
//    }

    public function getFeatureByProductId(int $productId){
        $qbSelectId = $this->connection->createQueryBuilder();
        return $qbSelectId
                ->select('ufd.id_unit_feature, ufd.id_product, ufd.id_product_attribute, uf.unit_feature_shortcut, ufv.value, uf.unit_feature_base_value')
                ->from($this->dbPrefix . 'unit_feature_product', 'ufd')
                ->where('id_product = :id_product')
                ->setParameter(':id_product', $productId)
            ->innerJoin('ufd', $this->dbPrefix . 'unit_feature', 'uf', 'ufd.id_unit_feature = uf.id_unit_feature')
            ->innerJoin('ufd', $this->dbPrefix . 'unit_feature_value', 'ufv', 'ufv.id_unit_feature_value = ufd.id_unit_feature_value')
            ->execute()
            ->fetchAllAssociative()
                ;

    }


    public function getFeatureByProductAttributeId(int $productAttributeId){
        $qbSelectId = $this->connection->createQueryBuilder();
        return $qbSelectId
            ->select('ufd.id_unit_feature, ufd.id_product, ufd.id_product_attribute, uf.unit_feature_shortcut, ufv.value, uf.unit_feature_base_value')
            ->from($this->dbPrefix . 'unit_feature_product', 'ufd')
            ->where('id_product_attribute = :id_product_attribute')
            ->setParameter(':id_product_attribute', $productAttributeId)
            ->innerJoin('ufd', $this->dbPrefix . 'unit_feature', 'uf', 'ufd.id_unit_feature = uf.id_unit_feature')
            ->innerJoin('ufd', $this->dbPrefix . 'unit_feature_value', 'ufv', 'ufv.id_unit_feature_value = ufd.id_unit_feature_value')
            ->execute()
            ->fetchAllAssociative()
            ;

    }
}
