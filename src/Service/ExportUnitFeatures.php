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

    public function exportFeatureProduct($featureProductRepository){
        $dataCallback = function ($offset, $limit) use ($featureProductRepository) {
            $featureProductRep = $featureProductRepository->findBy([], null, $limit, $offset);

            return array_map(function ($item) {
                return [
                    'id' => $item->getId(),
                    'id_unit_feature' => $item->getUnitFeature()->getId(),
                    'id_unit_feature_value' => $item->getUnitFeatureValue()->getId(),
                    'id_product' => $item->getIdProductAttribute(),
                    'id_product_attribute' => $item->getIdAttributeGroup(),
                ];
            }, $featureProductRep);

        };

        $headersData = [
            'id' => 'id',
            'id_unit_feature' => 'id_unit_feature',
            'id_unit_feature_value' => 'id_unit_feature_value',
            'id_product' => 'id_product',
            'id_product_attribute' => 'id_product_attribute',
        ];

        return $this->export($dataCallback, $headersData, 'feature_product');
    }

    public function exportFeatures($featuresRepository){
        $dataCallback = function ($offset, $limit) use ($featuresRepository) {
            $feature = $featuresRepository->findBy([], null, $limit, $offset);

            return array_map(function ($item) {
                return [
                    'id_unit_feature' => $item->getId(),
                    'unit_feature_name' => $item->getUnitFeatureName(),
                    'unit_feature_shortcut' => $item->getUnitFeatureShortcut(),
                    'unit_feature_base_value' => $item->getUnitFeatureBaseValue(),
                ];
            }, $feature);

        };

        $headersData = [
            'id_unit_feature' => 'id_unit_feature',
            'unit_feature_name' => 'unit_feature_name',
            'unit_feature_shortcut' => 'unit_feature_shortcut',
            'unit_feature_base_value' => 'unit_feature_base_value',
        ];

        return $this->export($dataCallback, $headersData, 'features');
    }

    public function exportFeatureValues($featureValuesRepository){
        $dataCallback = function ($offset, $limit) use ($featureValuesRepository) {
            $featureValue = $featureValuesRepository->findBy([], null, $limit, $offset);

            return array_map(function ($item) {
                return [
                    'id_unit_feature_value' => $item->getId(),
                    'id_unit_feature' => $item->getUnitFeature()->getId(),
                    'value' => $item->getValue(),
                ];
            }, $featureValue);

        };

        $headersData = [
            'id_unit_feature_value' => 'id_unit_feature_value',
            'id_unit_feature' => 'id_unit_feature',
            'value' => 'value'
        ];
        return $this->export($dataCallback, $headersData, 'feature_values');
    }

    private function export($data, $headersData, $fileNameSuffix=''){
        return (new CsvResponse())
            ->setData($data)
            ->setHeadersData($headersData)
            ->setLimit(5000)
            ->setModeType(CsvResponse::MODE_OFFSET)
            ->setFileName($this->getCsvFilename($fileNameSuffix));
    }
    private function getCsvFileName($suffix): string
    {
        return 'va_bulkfeaturemanager_export_'.$suffix.'_' . date('Ymd_His') . '.csv';
    }
}
