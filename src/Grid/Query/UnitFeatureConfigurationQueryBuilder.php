<?php

namespace Va_bulkfeaturemanager\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class UnitFeatureConfigurationQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private $searchCriteriaApplicator;
    public function __construct(Connection $connection, $dbPrefix, DoctrineSearchCriteriaApplicator $searchCriteriaApplicator)
    {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('uf.id_unit_feature, uf.unit_feature_name, uf.unit_feature_shortcut');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);
        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT uf.id_unit_feature)');
        ;
        return $qb;
    }

    public function getQueryBuilder(array $filters){
        $allowedFilters = [
            'id_unit_feature',
            'unit_feature_name',
            'unit_feature_shortcut'
        ];
        $qb = $this->connection
            ->createQueryBuilder()
            ->from('va_unit_feature', 'uf')
            ;
        foreach($filters as $name => $value){
            if(!in_array($name,$allowedFilters, true)){
                continue;
            }

            if('id_unit_feature' === $name){
                $qb->andWhere('uf.id_unit_feature = :'.$name);
                $qb->setParameter($name, $value);
                continue;
            }

            $qb->andWhere("$name LIKE :$name");
            $qb->setParameter($name, "%$value%");
        }

        return $qb;
    }
}
