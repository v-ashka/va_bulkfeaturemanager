<?php

namespace Va_bulkfeaturemanager\Grid\GridFeatureValueList\Filters;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Va_bulkfeaturemanager\Grid\GridFeatureValueList\Definition\Factory\GridFeatureValueDefinitionFactory;
class GridFeatureValueFilters extends Filters
{

    protected $filterId = GridFeatureValueDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 20,
            'offset' => 0,
            'orderBy' => 'id_unit_feature',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
