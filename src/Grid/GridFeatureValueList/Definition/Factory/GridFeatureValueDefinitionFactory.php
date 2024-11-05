<?php


declare(strict_types=1);

namespace Va_bulkfeaturemanager\Grid\GridFeatureValueList\Definition\Factory;


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

class GridFeatureValueDefinitionFactory extends AbstractGridDefinitionFactory
{


    const GRID_ID = 'gridFeatureValueList';
    const GRID_DOMAIN_TRANSLATOR = 'Modules.Va_bulkfeaturemanager.GridFeatureValueList';
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Feature value list', [], self::GRID_DOMAIN_TRANSLATOR);
    }

    protected function getColumns(){
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_unit_feature_value'
                ])
            )
            ->add((new DataColumn('id_unit_feature_value'))
                ->setName($this->trans('ID', [], self::GRID_DOMAIN_TRANSLATOR))
                ->setOptions([
                    'field' => 'id_unit_feature_value'
                ])
            )
            ->add((new DataColumn('value'))
                ->setName($this->trans('ID', [], self::GRID_DOMAIN_TRANSLATOR))
                ->setOptions([
                    'field' => 'value'
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'DELETE',
                                'route' => "va_bulkfeaturemanager_delete_feature",
                                'route_param_name' => "featureId",
                                'route_param_field' => "id_unit_feature_value",
                                'confirm_message' => $this->trans('Delete selected item?', [], 'Admin.Notifications.Warning')
                            ])
                        )
                        ->add((new SubmitRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => "va_bulkfeaturemanager_edit_feature",
                                'route_param_name' => "featureId",
                                'route_param_field' => "id_unit_feature_value",
//                                'clickable_row' => true,
                            ])
                        )
                ])
            )
            ;
    }

    protected function getFilters()
    {

        return (new FilterCollection())
            ->add((new Filter('id_unit_feature_value', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('ID', [], 'Admin.Advparameters.Feature')
                    ]
                ])
                ->setAssociatedColumn('id_unit_feature_value')
            )
            ->add((new Filter('value', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Value', [], 'Admin.Advparameters.Feature')
                    ]
                ])
                ->setAssociatedColumn('value')
            );
    }

}