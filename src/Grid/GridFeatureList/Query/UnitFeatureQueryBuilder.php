<?php

namespace Va_bulkfeaturemanager\Grid\GridFeatureList\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;


class UnitFeatureQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private $searchCriteriaApplicator;

    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    )
    {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * Get query search result from grid
     * @param array $filters
     * @return QueryBuilder
     */
    private function getGridQueryBuilder(array $filters): QueryBuilder{

        $allowedFilters = [
            'id_unit_feature',
            'unit_feature_name',
            'unit_feature_shortcut',
            'unit_feature_base_value'
        ];

        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'unit_feature', 'uf');

        foreach($filters as $name => $value){
            if(!in_array($name, $allowedFilters, true)){
                continue;
            }

            if('id_unit_feature' === $name){
                $qb->andWhere("uf.`id_unit_feature` = :id_unit_feature");
                $qb->setParameter(":id_unit_feature", $value);
                continue;
            }

            $qb->andWhere("$name LIKE :$name");
            $qb->setParameter($name, "%$value%");
        }

        return $qb;
    }

    /**
     * Select fields to query in grid.
     * You must select the columns that were provided in GridDefinitionFactory
     * @param SearchCriteriaInterface $searchCriteria
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
//        create query builder on search criteria
        $qb = $this->getGridQueryBuilder($searchCriteria->getFilters());
//        Select id_unit_feature, feature_name, feature_shortcut and feature_base_value to display values in grid
        $qb->select('uf.id_unit_feature, uf.unit_feature_name, uf.unit_feature_shortcut, uf.unit_feature_base_value');

//        add pagination and sorting to grid
        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * Display amount of elements in grid
     * @param SearchCriteriaInterface $searchCriteria
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getGridQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT uf.id_unit_feature)');

        return $qb;
    }
}
