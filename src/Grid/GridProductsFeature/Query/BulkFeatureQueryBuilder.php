<?php


declare(strict_types=1);
namespace Va_bulkfeaturemanager\Grid\GridProductsFeature\Query;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class BulkFeatureQueryBuilder extends AbstractDoctrineQueryBuilder{
    private $searchCriteriaApplicator;

    public function __construct(Connection $connection, $dbPrefix, DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator)
    {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * Select fields to query in grid.
     * You must select the columns that were provided in GridDefinitionFactory
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
//        dd($searchCriteria);
//        Create query builder with combined columns from getQueryBuilder() method
        $qb = $this->getQueryBuilder();
        $qb
//            This query builder return fields: id_product, product_name, product_category, feature_name, feature_value
            ->select('p.id_product, pl.name, pc.name as category, ufv.value as feature_value')
            ->addSelect("(
        SELECT DISTINCT uf.unit_feature_name FROM " . $this->dbPrefix . "unit_feature uf
        LEFT JOIN " . $this->dbPrefix . "unit_feature_product ufp ON ufp.id_unit_feature = uf.id_unit_feature
        WHERE ufp.id_product = p.id_product LIMIT 1) AS feature_id_name")
            ->groupBy('p.id_product')
            ->addGroupBy('pl.name')
            ->addGroupBy('pc.name')
        ;
//      Apply pagination and sorting functionality to grid
        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb)
        ;

        return $qb;

    }

    /**
     * Get query search result from grid
     * @param array $filters
     */
    public function getGridQueryBuilder(array $filters){

        $allowedFilters = [
            "id_product",
            "reference",
            "name",
            "category",
            "feature_value",
            "feature_id_name",
            "ean13",
            "isbn"
        ];

        $qb = $this->getQueryBuilder();
        $qb->select('pl.id_product as id_product, pc.name as category, ufv.value as feature_value, ufp.id_unit_feature, p.ean13, p.isbn, p.reference');
        foreach($filters as $name => $value){
            if(!in_array($name, $allowedFilters, true)){
                continue;
            }

            if('id_product' === $name){
                $qb->andWhere("ufp.`id_product` = :id_product");
                $qb->setParameter(":id_product", $value);
                continue;
            }

            if('reference' === $name){
                $qb->andWhere("p.`reference` = :reference");
                $qb->setParameter(":reference", $value);
                continue;
            }

            if('ean13' === $name){
                $qb->andWhere("p.`ean13` = :ean13");
                $qb->setParameter(":ean13", $value);
                continue;
            }

            if('category' === $name){
                $qb->andWhere("pc.`name` = :category");
                $qb->setParameter(":category", $value);
                continue;
            }



            $qb->andWhere("$name LIKE :$name");
            $qb->setParameter($name, "%$value%");
        }

        return $qb;
    }

    /**
     * Get amount of elements in grid
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder()
        ->select('COUNT(p.id_product)');
        return $qb;
    }

    /**
     * Combine all feature columns together using left join
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQueryBuilder(){
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'product', 'p')
            ->innerJoin('p', $this->dbPrefix.'product_lang', 'pl', 'p.id_product = pl.id_product')
            ->leftJoin('p', $this->dbPrefix.'category_lang', 'pc', 'p.id_category_default = pc.id_category')
            ->leftJoin('p', $this->dbPrefix. 'unit_feature_product', 'ufp', 'p.id_product = ufp.id_product')
            ->leftJoin('ufp', $this->dbPrefix . 'unit_feature_value', 'ufv', 'ufp.id_unit_feature_value = ufv.id_unit_feature_value')
            ;
        return $qb;
    }
}
