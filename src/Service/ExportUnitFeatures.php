<?php

namespace Va_bulkfeaturemanager\Service;
use PrestaShopBundle\Component\CsvResponse;
use Va_bulkfeaturemanager\Form\AdminProductsExtraConfiguration\UnitFeatureProductsExtraDataProvider;
class ExportUnitFeatures
{
    private $featureProduct;
    public function __construct(UnitFeatureProductsExtraDataProvider $featureProduct)
    {
        $this->featureProduct = $featureProduct;
    }

    public function exportFeatureProduct(array $features){
        $featureProduct = $this->featureProduct;



        $headersData = [
            'id' => 'ID',
            'id_unit_feature' => 'id_unit_feature',
            'id_unit_feature_value' => 'id_unit_feature_value',
            'id_product' => 'id_product',
            'id_product_attribute' => 'id_product_attribute',
            'id_attribute_group' => 'id_attribute_group',
        ];

        return (new CsvResponse())
            ->setData($features)
            ->setHeadersData($headersData)
            ->setLimit(5000)
            ->setFileName($this->getCsvFilename());
    }

    private function getCsvFileName(): string
    {
        return 'va_bulkfeaturemanager_export' . date('Ymd_His') . '.csv';
    }


    public function exportFeatures(array $features){
//        $features = $this->feature
        $headersData = [
            'id_unit_feature' => 'id_unit_feature',
            'unit_feature_name' => 'unit_feature_name',
            'unit_feature_shortcut' => 'unit_feature_shortcut',
            'unit_feature_base_value' => 'unit_feature_base_value',
        ];
    }

    public function exportFeatureValues(array $featureValues){
        $headersData = [
            'id_unit_feature_value' => 'id_unit_feature_value',
            'id_unit_feature' => 'id_unit_feature',
            'value' => 'value'
        ];
    }
}
