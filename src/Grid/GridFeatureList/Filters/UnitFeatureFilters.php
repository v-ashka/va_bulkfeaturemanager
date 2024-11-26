<?php

namespace Va_bulkfeaturemanager\Grid\GridFeatureList\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use Va_bulkfeaturemanager\Grid\GridFeatureList\Definition\Factory\UnitFeatureDefinitionFactory;

class UnitFeatureFilters extends Filters
{
    protected $filterId = UnitFeatureDefinitionFactory::GRID_ID;

//    get defaults filter for grid
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
