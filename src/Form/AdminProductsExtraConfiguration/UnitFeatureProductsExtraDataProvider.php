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
    private $unitFeatureRepository;
    private $unitFeatureValueRepository;

    public function __construct(UnitFeatureProductRepository $repository, UnitFeatureRepository $unitFeatureRepository, UnitFeatureValueRepository $unitFeatureValueRepository)
    {
        $this->productRepository = $repository;
        $this->unitFeatureRepository = $unitFeatureRepository;
        $this->unitFeatureValueRepository = $unitFeatureValueRepository;
    }

    public function getData($id)
    {
//        dd($this->repository);
//        dd($this->repository->findBy(['idProductAttribute' => $id]), $id);
        $featureProductRepository = $this->productRepository->findOneBy(['idProductAttribute' => $id]);
        $extraAdminUnitFeature = [];
        if($featureProductRepository != null) {
            $extraAdminUnitFeature = [
//                'id' => $featureProductRepository->getId(),
                'feature_id' => $featureProductRepository->getUnitFeature()->getId(),
                'feature_id_val' => $featureProductRepository->getUnitFeatureValue()->getId(),
//                'id_product' => $featureProductRepository->getidProductAttribute(),
            ];
            dump($featureProductRepository);
//            dd($featureProduct);
//            $unitFeatureRepository = $this->unitFeatureRepository->findOneById($featureProduct['id_unit_feature']);
//            $unitFeatureValueRepository = $this->unitFeatureValueRepository->findOneById('id_unit_feature_value');

//            $extraAdminUnitFeature = [
//                'feature_id' => $featureProduct->getUnitFeature(),
//                'feature_id_val' => $featureProduct->getUnitFeatureValue()
//            ];
        }else{
           $extraAdminUnitFeature = $this->getDefaultData();
        }
//        dd($featureProductRepository, $featureProductRepository->getUnitFeature()->getId(), $this->productRepository->findAll());

        return $extraAdminUnitFeature;
    }

    public function getDefaultData()
    {
        return [
            'feature_id' => '',
            'feature_id_val' => '',
        ];
    }
}
