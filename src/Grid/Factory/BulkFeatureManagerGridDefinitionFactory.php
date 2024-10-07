<?php

namespace Va_bulkfeaturemanager\Grid\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;

class BulkFeatureManagerGridDefinitionFactory extends AbstractGridDefinitionFactory
{

    protected function getId()
    {
        return 'bulkFeatureManagerGrid';
    }

    protected function getName()
    {
        return $this->trans('Products with features', [], 'Admin.Advparameters.Feature');
    }

    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_product'))
                ->setName($this->trans('ID', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'id_product',
                ])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'name'
                ])
            );
    }
}
