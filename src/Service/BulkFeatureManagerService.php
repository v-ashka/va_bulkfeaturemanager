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

    /**
     * This functions checks if feature exists in a specific product id
     * @param $qbSelect
     * @param $itemId
     * @return mixed
     */
    private function findExistingFeature($qbSelect, $itemId){
       return $qbSelect
            ->select('COUNT(1)')
            ->from($this->dbPrefix . 'unit_feature_product')
            ->andWhere("id_product = :id_product")
            ->setParameter(':id_product',  $itemId)
            ->execute()
            ->fetchOne();
    }

    /**
     * Add feature to selected product and save it to database
     * @param int $featureId
     * @param int $featureValueId
     * @param array $productsId
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function addFeaturesToProducts(int $featureId, int $featureValueId, array $productsId): array
    {
        // This array collects logs about the transaction.
        // In positive cases, it returns "success" values, and in negative or warning cases, it returns "warning" values.
        $transactionLog = [];
        // Start transaction
        $this->connection->beginTransaction();
        try{
            foreach($productsId as $itemId){
                // Before insert, check if featureId is not related already with productId
                $qbSelect = $this->connection->createQueryBuilder();
                $validFeature = $this->findExistingFeature($qbSelect, $itemId);

                // create query builder action
                $qbAction = $this->connection->createQueryBuilder();
                    // If product have not assigned any values
                    if(!$validFeature){
                        // Insert values to unit_feature_product
                        $qbAction->insert($this->dbPrefix . 'unit_feature_product')
                            ->values([
                                'id_unit_feature' => ':id_unit_feature',
                                'id_product' => ':id_product',
                                'id_unit_feature_value' => ':id_unit_feature_value'
                            ])
                            ->setParameter(':id_unit_feature', $featureId)
                            ->setParameter(':id_product', $itemId)
                            ->setParameter(':id_unit_feature_value', $featureValueId)
                            ->execute();
                    }else{
                    // Otherwise update feature values in this product
                        $qbAction->update($this->dbPrefix . 'unit_feature_product')
                            ->set('id_unit_feature', ':id_unit_feature')
                            ->set('id_product', ':id_product')
                            ->set('id_unit_feature_value', ':id_unit_feature_value')
                            ->where('id_product = :id_product')
                            ->setParameter(':id_unit_feature', $featureId)
                            ->setParameter(':id_product', $itemId)
                            ->setParameter(':id_unit_feature_value', $featureValueId)
                            ->execute();
                    }
                    // If feature is overwriting, add product_id to transactionLog array and mark it as overwrite "warning", otherwise mark it as 'success'
                    !$validFeature  ? ($transactionLog['success'][] = $itemId):($transactionLog['warning'][] = $itemId);
            }
            $this->connection->commit();
        } catch(\Exception $e){
            $this->connection->rollBack();
            throw $e;
        }
        return $transactionLog;
    }

    /**
     * Remove features from selected products
     * @param int $featureId
     * @param array $productsId
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function removeFeatureFromProducts(int $featureId, array $productsId): array
    {
        $transactionLog = [];
        $this->connection->beginTransaction();
        try{
            // Check every product
            foreach($productsId as $itemId){
                $qbDelete = $this->connection->createQueryBuilder();
                // If selected feature is assigned to selected product
                $validFeature = $this->findExistingFeature($qbDelete, $itemId);
                if($validFeature){
                    // Do delete action on specific id_product
                    $qbDelete
                        ->delete($this->dbPrefix . 'unit_feature_product')
                        ->where('id_product = :id_product')
                        ->setParameter(":id_product", $itemId)
                        ->execute()
                    ;
                    // If success, add product id to transaction log array and mark it as "success"
                    $transactionLog['success'][] = $itemId;
                }else{
                    $transactionLog['warning'][] = $itemId;
                }
            }
            // Send changes to database
            $this->connection->commit();
        } catch(\Exception $e){
            // In any exception rollback changes
            $this->connection->rollBack();
            throw $e;
        }
        return $transactionLog;
    }

    /**
     * Delete all features from a product
     * In future versions, this function will be removed because it becomes redundant if removeFeatureFromProducts() function exists.
     * @param array $productsId
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
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
     * Deprecated function
     * Get feature attached to specific productId
     * Returns columns (id_unit_feature, id_product, id_product_attribute, feature_shortcut, feature_value, feature_base_value)
     * @param int $productId
     * @return array|\mixed[][]
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
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

    /**
     * Get features attached by productAttributeId
     * Returns columns (id_unit_feature, id_product, id_product_attribute, feature_shortcut, feature_value, feature_base_value)
     * @param int $productAttributeId
     * @return array|\mixed[][]
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
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

    /**
     * Get features from database
     * @return array
     */
    public function getFeatures(){
        $features = [];
        $sql = "SELECT id_unit_feature, unit_feature_name FROM " . _DB_PREFIX_ . "unit_feature";
        $res = \Db::getInstance()->executeS($sql);

        // Assign each value to array
        foreach($res as $row){
            $rowName = $row['unit_feature_name'] . ' (id:'.$row['id_unit_feature'] .' )';
            $features[$rowName] = $row['id_unit_feature'];
            // After string modifications array will look like:
            // ex. ["Weight (id: 1 )" => 1 ]
        }
        return $features;
    }

    /**
     * get Feature Values list
     * @param bool $insertFeatureId decide if you want to provide featureId as attribute. Default is set to false
     * @return array
     */
    public function getFeatureValues(bool $insertFeatureId = false): array
    {
        $featureVals = [];
        //        sql query
        $sql = "SELECT
                ufv.id_unit_feature_value, ufv.id_unit_feature, ufv.value, uf.unit_feature_shortcut
                FROM " . _DB_PREFIX_. "unit_feature_value ufv
                INNER JOIN "._DB_PREFIX_. "unit_feature uf
                ON uf.id_unit_feature = ufv.id_unit_feature" ;
        $res = \Db::getInstance()->executeS($sql);

        //Assign each value to array
        foreach($res as $row){
            $rowName = $row['value'] .' (id: ' . $row['id_unit_feature_value']. ' | '. $row['unit_feature_shortcut'] .' )';
            $featureVals[$rowName] = $row['id_unit_feature_value'];
            // After string modifications array will look like:
            // ex. ["300 (id: 1 | kg )" => 1 ]

            // If featureId is provide as attribute
            if($insertFeatureId){
            // Add data attribute to rowName. This value is used to manipulate view data in javascript
                $featureVals[$rowName] = array('data-feature_id' => $row['id_unit_feature']);
            }

        }

        return $featureVals;
    }
}
