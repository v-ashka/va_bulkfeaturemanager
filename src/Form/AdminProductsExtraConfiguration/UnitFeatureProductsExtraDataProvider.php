<?php

namespace Va_bulkfeaturemanager\Form\AdminProductsExtraConfiguration;



use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use Va_bulkfeaturemanager\Repository\UnitFeatureProductsExtraRepository;
use Va_bulkfeaturemanager\Repository\UnitFeatureProductRepository;


class UnitFeatureProductsExtraDataProvider implements FormDataProviderInterface
{
    private $repository;

    public function __construct(UnitFeatureProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getData($id)
    {
////        dd($this->repository);
//        dd($this->repository->findBy(['idProductAttribute' => $id]), $id);
//        $featureProduct = $this->repository->findBy(['id_product' => $id]);
//
//
//        $extraAdminUnitFeature = [
//            'id_product' => $featureProduct->getId(),
//            'feature_id' => $featureProduct->getUnitFeature(),
//            'feature_id_val' => $featureProduct->getUnitFeatureValue()
//        ];
            $extraAdminUnitFeature = [];
        return $extraAdminUnitFeature;
    }

    public function getDefaultData()
    {
        return [
            'id_product' => '',
            'feature_id' => '',
            'feature_id_val' => '',
        ];
    }
}
