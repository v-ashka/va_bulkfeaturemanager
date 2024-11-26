<?php


declare(strict_types=1);

namespace Va_bulkfeaturemanager\Grid\GridFeatureValueList\Definition\Factory;


use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
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
    /**
     * @var Request
     */
    private $featureId;

    /**
     * Get grid id
     * @return string
     */
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    /**
     * Get grid name
     * @return string
     */
    protected function getName(): string
    {
        return $this->trans('Feature value list', [], self::GRID_DOMAIN_TRANSLATOR);
    }

    /**
     * Set featureId for route redirection
     * @param $featureId
     * @return int|mixed
     */
    public function setFeatureId($featureId = 1)
    {
        return $this->featureId = $featureId;
    }

    /**
     * Get featureId
     * @return Request
     */
    public function getFeatureId(){
        return $this->featureId;
    }

    /**
     * Display columns in grid (id_unit_feature_value, value ) and actions (delete, edit)
     * @return ColumnCollection|\PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface
     */
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
                ->setName($this->trans('Featue value', [], self::GRID_DOMAIN_TRANSLATOR))
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
//                                HTTP method
                                'method' => 'DELETE',
//                                route redirection after DELETE method
                                'route' => "va_bulkfeaturemanager_feature_value_delete",
//                                route parameters to use
                                'route_param_name' => "featureValueId",
//                                route parameter key value
                                'route_param_field' => "id_unit_feature_value",
//                                confirmation message
                                'confirm_message' => $this->trans('Delete selected item?', [], 'Admin.Notifications.Warning')
                            ])
                        )
                        ->add((new SubmitRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
//                                route to redirect after select Edit button
                                'route' => "va_bulkfeaturemanager_feature_value_edit",
//                                param to send
                                'route_param_name' => "featureValueId",
//                                route param key value
                                'route_param_field' => "id_unit_feature_value",
                            ])
                        )
                ])
            )
            ;
    }

//    Display filters inputs in grid
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
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setAssociatedColumn('actions')
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID,
                    ],
                    'redirect_route' => 'va_bulkfeaturemanager_feature_values',
                    'redirect_route_params' => [
                        'featureId' => $this->featureId
                    ]

                ])
            )
            ;
    }

//    Add bulk action to grid
    protected function getBulkActions(){
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('action_feature'))
                ->setName($this->trans('Delete selected', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'submit_route' => 'va_bulkfeaturemanager_feature_value_delete_bulk',
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Advparameters.Feature')
                ])
            )
            ;
    }


}
