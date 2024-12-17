<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */
declare(strict_types=1);

namespace Va_bulkfeaturemanager\Grid\GridProductsFeature\Filters;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Va_bulkfeaturemanager\Grid\GridProductsFeature\Factory\BulkFeatureManagerGridDefinitionFactory;

class BulkFeatureManagerFilters extends Filters
{
    protected $filterId = BulkFeatureManagerGridDefinitionFactory::GRID_ID;

    /**
     * Get defaults filter values
     * @return int[]
     */
    public static function getDefaults(): array
    {
        return [
            'limit' => 15,
            'offset' => 0,
            'orderBy' => 'id_product',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
