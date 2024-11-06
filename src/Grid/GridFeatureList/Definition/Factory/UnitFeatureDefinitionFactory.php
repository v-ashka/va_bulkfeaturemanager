<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Grid\GridFeatureList\Definition\Factory;


use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
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

class UnitFeatureDefinitionFactory extends AbstractGridDefinitionFactory
{

    const GRID_ID = 'gridFeatureList';
    const GRID_DOMAIN_TRANSLATOR = 'Modules.Va_bulkfeaturemanager.GridFeatureList';
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Feature List', [], 'Modules.Va_bulkfeaturemanager.GridFeatureList');
    }

    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_unit_feature'
                ])
            )
            ->add((new DataColumn('id_unit_feature'))
                ->setName($this->trans('ID', [], self::GRID_DOMAIN_TRANSLATOR))
                ->setOptions([
                    'field' => 'id_unit_feature'
                ])
            )
            ->add((new DataColumn('unit_feature_name'))
                ->setName($this->trans('Feature name', [], self::GRID_DOMAIN_TRANSLATOR))
                ->setOptions([
                    'field' => 'unit_feature_name'
                ])
            )
            ->add((new DataColumn('unit_feature_shortcut'))
                ->setName($this->trans('Feature shortcut', [], self::GRID_DOMAIN_TRANSLATOR))
                ->setOptions([
                    'field' => 'unit_feature_shortcut'
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new SubmitRowAction('show'))
                            ->setName($this->trans('Show feature values', [], 'Admin.Actions'))
                            ->setIcon('searchpin')
                            ->setOptions([
                                'route' => "va_bulkfeaturemanager_feature_values",
                                'route_param_name' => "featureId",
                                'route_param_field' => "id_unit_feature",
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'DELETE',
                                'route' => "va_bulkfeaturemanager_delete_feature",
                                'route_param_name' => "featureId",
                                'route_param_field' => "id_unit_feature",
                                'confirm_message' => $this->trans('Delete selected item?', [], 'Admin.Notifications.Warning')
                            ])
                        )
                        ->add((new SubmitRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => "va_bulkfeaturemanager_edit_feature",
                                'route_param_name' => "featureId",
                                'route_param_field' => "id_unit_feature",

                            ])
                        )
                ])
            )
            ;
    }

    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_unit_feature', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('ID', [], 'Admin.Advparameters.Feature')
                    ]
                ])
                ->setAssociatedColumn('id_unit_feature')
            )
            ->add((new Filter('unit_feature_name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Feature name', [], 'Admin.Advparameters.Feature')
                    ]
                ])
                ->setAssociatedColumn('unit_feature_name')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setAssociatedColumn('actions')
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID
                    ],
                    'redirect_route' => 'va_bulkfeaturemanager_features_list',
                ])
            )
            ;
    }
}
