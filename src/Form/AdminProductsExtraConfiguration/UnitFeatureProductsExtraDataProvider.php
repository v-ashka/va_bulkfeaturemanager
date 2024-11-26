<?php

namespace Va_bulkfeaturemanager\Form\AdminProductsExtraConfiguration;



use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use Va_bulkfeaturemanager\Repository\UnitFeatureProductsExtraRepository;
use Va_bulkfeaturemanager\Repository\UnitFeatureProductRepository;
use Va_bulkfeaturemanager\Repository\UnitFeatureRepository;
use Va_bulkfeaturemanager\Repository\UnitFeatureValueRepository;


class UnitFeatureProductsExtraDataProvider implements FormDataProviderInterface
{
    private $productRepository;

    public function __construct(UnitFeatureProductRepository $repository)
    {
        $this->productRepository = $repository;
    }

    /**
     * Get data by product id
     * @param $id
     * @return array|string[]
     */
    public function getData($id): array
    {
//        Find feature values in provided product id
        $featureProductRepository = $this->productRepository->findOneBy(['idProductAttribute' => $id]);
        if($featureProductRepository != null) {
//            If exists fetch values from database
            $extraAdminUnitFeature = [
                'feature_id' => $featureProductRepository->getUnitFeature()->getId(),
                'feature_id_val' => $featureProductRepository->getUnitFeatureValue()->getId(),
            ];
        }else{
//            If not exists get default data
           $extraAdminUnitFeature = $this->getDefaultData();
        }
        return $extraAdminUnitFeature;
    }

    /**
     * Return default data
     * @return string[]
     */
    public function getDefaultData(): array
    {
        return [
            'feature_id' => '',
            'feature_id_val' => '',
        ];
    }
}
