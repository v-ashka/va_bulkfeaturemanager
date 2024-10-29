<?php

namespace Va_bulkfeaturemanager\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use Va_bulkfeaturemanager\Grid\Factory\UnitFeatureConfigurationDefinitionFactory;

class UnitFeatureConfigurationFilters extends Filters
{
    protected $filterId = UnitFeatureConfigurationDefinitionFactory::GRID_ID;


    public static function getDefaults(): array
    {
        return [
            'limit' => 50,
            'offset' => 0,
            'orderBy' => 'id_unit_feature',
            'sortOrder' => 'DESC',
            'filters' => []
        ];
    }

}
