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

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('p.id_product, pl.name, pc.name as category')
            ->addSelect("(
        SELECT DISTINCT uf.unit_feature_name FROM " . $this->dbPrefix . "unit_feature uf
        LEFT JOIN " . $this->dbPrefix . "unit_feature_product ufp ON ufp.id_unit_feature = uf.id_unit_feature
        WHERE ufp.id_product = p.id_product LIMIT 1) AS feature_id_name")
//            ->select('p.id_product, pl.name, pc.name as category')
            ->groupBy('p.id_product')
            ->addGroupBy('pl.name')
            ->addGroupBy('pc.name')
        ;

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb)
        ;

        return $qb;

    }


    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters())
        ->select('COUNT(p.id_product)');

        return $qb;
    }

    public function getQueryBuilder(){
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'product', 'p')
            ->innerJoin('p', $this->dbPrefix.'product_lang', 'pl', 'p.id_product = pl.id_product')
            ->leftJoin('p', $this->dbPrefix.'category_lang', 'pc', 'p.id_category_default = pc.id_category')

            ;

        return $qb;
    }
}
