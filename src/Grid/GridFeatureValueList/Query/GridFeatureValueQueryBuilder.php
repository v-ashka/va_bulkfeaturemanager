<?php
declare(strict_types=1);
namespace Va_bulkfeaturemanager\Grid\GridFeatureValueList\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Component\HttpFoundation\Request;

class GridFeatureValueQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private $searchCriteriaApplicator;
    private $featureId;
    public function __construct(
        Connection $connection,
                   $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ){
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * Set feature id for route redirection purpose
     * @param int $featureId
     * @return void
     */
    public function setFeatureId(int $featureId){
        $this->featureId = $featureId;
    }

    /**
     * Create grid query builder with filter dependencies
     * @param array $filters
     * @return QueryBuilder
     */
    private function getGridQueryBuilder(array $filters){
//        Allowed values to filter
        $allowedFilters = [
            'id_unit_feature_value',
            'value'
        ];
//        Create DBAL query builder
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'unit_feature_value', 'ufv');
        $qb->where("ufv.`id_unit_feature` = :id_unit_feature");
        $qb->setParameter(':id_unit_feature', $this->featureId);

//        Select filtered values in grid
        foreach ($filters as $name => $value){
            if(!in_array($name, $allowedFilters, true)){
                continue;
            }

            if('id_unit_feature_value' === $name){
                $qb->andWhere("ufv.`id_unit_feature_value` = :id_unit_feature_value");

                $qb->setParameter(':id_unit_feature_value', $value);
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
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getGridQueryBuilder($searchCriteria->getFilters());
        $qb->select('ufv.id_unit_feature_value', 'ufv.value');

//        Add pagination and sorting functionality to grid
        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;
//       Return query builder
        return $qb;
    }
    /**
     * Display amount of elements in grid
     * @param SearchCriteriaInterface $searchCriteria
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getGridQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT ufv.id_unit_feature_value)');

        return $qb;
    }
}
