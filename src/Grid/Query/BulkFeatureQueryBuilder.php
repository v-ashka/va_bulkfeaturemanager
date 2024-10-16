<?php


declare(strict_types=1);
namespace Va_bulkfeaturemanager\Grid\Query;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
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
            ->groupBy('p.id_product');

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

    public function getQueryBuilder(array $filters){
        $allowedFilter = [
            'id_product',
            'name',
            'category',
        ];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'product', 'p')
            ->innerJoin('p', $this->dbPrefix.'product_lang', 'pl', 'p.id_product = pl.id_product')
            ->leftJoin('p', $this->dbPrefix.'category_lang', 'pc', 'p.id_category_default = pc.id_category')
            ;
        foreach($filters as $name => $value){
            if(!in_array($name, $allowedFilter, true)){
                continue;
            }

            if('id_product' === $name){
                $qb->andWhere('p.`id_product` = :' . $name);
                $qb->setParameter($name, $value);

                continue;
            }
            if('name' === $name){
                $qb->andWhere("pl.`name` LIKE :$name");
                $qb->setParameter($name, "%$value%");
            }

            if('category' === $name){
                $qb->andWhere("pc.`category` LIKE :$name");
                $qb->setParameter($name, "%$value%");
            }

        }

        return $qb;
    }
}
