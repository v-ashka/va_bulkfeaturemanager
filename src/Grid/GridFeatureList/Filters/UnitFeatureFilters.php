<?php

namespace Va_bulkfeaturemanager\Grid\GridFeatureList\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use Va_bulkfeaturemanager\Grid\GridFeatureList\Definition\Factory\UnitFeatureDefinitionFactory;

class UnitFeatureFilters extends Filters
{
    protected $filterId = UnitFeatureDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 25,
            'offset' => 0,
            'orderBy' => 'uf.id_unit_feature',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
