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
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
    )
    {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }
    private function getGridQueryBuilder(array $filters): QueryBuilder{

        $allowedFilters = [
            'id_unit_feature',
            'unit_feature_name',
            'unit_feature_shortcut'
        ];

        $qb = $this->connection->createQueryBuilder();
        $qb->from('va_unit_feature', 'uf');

        foreach($filters as $name => $value){
            if(!in_array($name, $allowedFilters, true)){
                continue;
            }

            if('id_unit_feature' === $name){
                $qb->andWhere("uf.`id_unit_feature` = :id_unit_feature");
//                ->where("id_feature = :id_feature")
//                    ->andWhere("id_product = :id_product")
//                    ->setParameter(":id_feature", $featureId)
                $qb->setParameter(":id_unit_feature", $value);
                continue;
            }

            $qb->andWhere("$name LIKE :$name");
            $qb->setParameter($name, "%$value%");
        }

        return $qb;
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getGridQueryBuilder($searchCriteria->getFilters());

        $qb->select('uf.id_unit_feature, uf.unit_feature_name, uf.unit_feature_shortcut')
//            ->groupBy('uf.id_unit_feature')
        ;

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getGridQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT uf.id_unit_feature)');

        return $qb;
    }
}
