<?php

namespace Va_bulkfeaturemanager\Grid\GridProductsFeature\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BulkFeatureManagerGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'bulkFeatureManagerGrid';

    protected function getId()
    {
        return self::GRID_ID;
    }

    protected function getName()
    {
        return $this->trans('Products with features', [], 'Admin.Advparameters.Feature');
    }

    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_product',
                ])
            )
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
            )
            ->add((new DataColumn('category'))
                ->setName($this->trans('Category', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'category',
                ])
            )
            ->add((new DataColumn('feature_id_name'))
                ->setName($this->trans('Feature name', [], 'Admin.Advparameters.Feature'))
            ->setOptions([
                'field' => 'feature_id_name',
            ])
            )
            ->add((new DataColumn('feature_value'))
                ->setName($this->trans('Assigned value', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'feature_value',
                ])
            )
            ->add((new ActionColumn('feature_action'))
                ->setName($this->trans('Action', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new SubmitRowAction('show'))
                            ->setName($this->trans('Go to product', [], 'Admin.Actions'))
                            ->setIcon('visibility')
                            ->setOptions([
                                'route' => "admin_products_edit",
                                'route_param_name' => "productId",
                                'route_param_field' => "id_product",
                            ])
                        )
                ])
            )
            ;

    }

    protected function getBulkActions(){
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('action_feature'))
                ->setName($this->trans('Refresh page', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'submit_route' => 'va_bulkfeaturemanager',
                ])
            )
            ;
    }
}
