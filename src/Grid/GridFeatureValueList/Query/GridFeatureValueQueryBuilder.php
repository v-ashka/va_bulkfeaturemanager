<?php
declare(strict_types=1);
namespace Va_bulkfeaturemanager\Grid\GridFeatureValueList\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
class GridFeatureValueQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private $searchCriteriaApplicator;

    public function __construct(
        Connection $connection,
                   $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ){
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    private function getGridQueryBuilder(array $filters){
        $allowedFilters = [
            'id_unit_feature_value',
            'value'
        ];

        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'unit_feature_value', 'ufv');

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

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getGridQueryBuilder($searchCriteria->getFilters());
        $qb->select('ufv.id_unit_feature_value', 'ufv.value');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getGridQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT ufv.id_unit_feature_value)');

        return $qb;
    }
}
