<?php

namespace Va_bulkfeaturemanager\Grid\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class UnitFeatureConfigurationDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'unitFeatureConfigurationGrid';

    protected function getId()
    {
        return self::GRID_ID;
    }

    protected function getName()
    {
        return $this->trans('Features list', [], 'Admin.Va_bulkfeaturemanager.UnitFeatureConfiguration');
    }

    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_unit_feature',
                ])
            )
            ->add((new DataColumn('id_unit_feature'))
                ->setName($this->trans('ID', [], 'Admin.Va_bulkfeaturemanager.UnitFeatureConfiguration'))
                ->setOptions([
                    'field' => 'id_unit_feature',
                ])
            )
            ->add((new DataColumn('unit_feature_name'))
                ->setName($this->trans('Unit name', [], 'Admin.Va_bulkfeaturemanager.UnitFeatureConfiguration'))
                ->setOptions([
                    'field' => 'unit_feature_name'
                ])
            )
            ->add((new DataColumn('unit_feature_shortcut'))
                ->setName($this->trans('Unit feature shortcut', [], 'Admin.Va_bulkfeaturemanager.UnitFeatureConfiguration'))
                ->setOptions([
                    'field' => 'unit_feature_shortcut',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setOptions([
                  'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setOptions([
                                'route' => 'va_bulkfeaturemanager',
                                'route_param_name' => 'unitFeatureId',
                                'route_param_field' => 'id_unit_feature',
                                'clickable_row' => true,
                            ])
                        )
                      ->add((new SubmitRowAction('delete'))
                          ->setIcon('edit')
                          ->setName($this->trans('Delete', [], 'Admin.Actions'))
                          ->setOptions([
                              'method' => 'DELETE',
                              'route' => 'va_bulkfeaturemanager',
                              'route_param_name' => 'unitFeatureId',
                              'route_param_field' => 'id_unit_feature',
                              'confirm_message' => $this->trans('Delete selected item?', [], 'Admin.Va_bulkfeaturemanager.UnitFeatureConfiguration'),
                          ])
                      )
                ])
            )

            ;
    }

    protected function getFilters(){
        return (new FilterCollection())
            ->add((new Filter('id_unit_feature', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('ID', [], 'Admin.Global')
                    ]
                ])
                ->setAssociatedColumn('id_unit_feature')
            )
            ->add((new Filter('unit_feature_name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Feature Name', [], 'Admin.Global')
                    ]
                ])
                ->setAssociatedColumn('unit_feature_name')
            )
            ->add((new Filter('unit_feature_shortcut', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Feature shortcut', [], 'Admin.Global')
                    ]
                ])
                ->setAssociatedColumn('unit_feature_shortcut')

            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID
                    ],
                    'redirect_route' => 'va_bulkfeaturemanager'
                ])
                ->setAssociatedColumn('actions')

            )
            ;
    }

    protected function getBulkActions(){
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('delete_bulk'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'va_bulkfeaturemanager',
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Actions')
                ])
            );
    }
}
