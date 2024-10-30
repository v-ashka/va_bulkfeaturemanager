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
//            ->select('p.id_product, pl.name, pc.name as category, fl.name as feature_id_name')
            ->select('p.id_product, pl.name, pc.name as category')
            ->addSelect("(
        SELECT GROUP_CONCAT(DISTINCT fl.name ORDER BY fl.id_feature ASC SEPARATOR ', ')
        FROM " . $this->dbPrefix . "feature_product fp
        LEFT JOIN " . $this->dbPrefix . "feature_lang fl ON fp.id_feature = fl.id_feature
        WHERE fp.id_product = p.id_product) AS feature_id_name")
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
//        $allowedFilter = [
//            'id_product',
//            'name',
//            'category',
//        ];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'product', 'p')
            ->innerJoin('p', $this->dbPrefix.'product_lang', 'pl', 'p.id_product = pl.id_product')
//            ps_category_lang(id_category, name)
            ->leftJoin('p', $this->dbPrefix.'category_lang', 'pc', 'p.id_category_default = pc.id_category')
//            ps_feature_product(id_feature, id_product, id_feature_value)
//            ->leftJoin('p', $this->dbPrefix.'feature_product', 'fp', 'p.id_product = fp.id_product')
////            ps_feature_lang(id_feature, name)
//            ->rightJoin('fp', $this->dbPrefix.'feature_lang', 'fl', 'fp.id_feature = fl.id_feature')
////            ps_feature_value_lang (id_feature_value, value)
//            ->leftJoin('fp', $this->dbPrefix.'feature_value_lang', 'fvl', 'fp.id_feature_value = fvl.id_feature_value')
            ;

        return $qb;
    }
}
