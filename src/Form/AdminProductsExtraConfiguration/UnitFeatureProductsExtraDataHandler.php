<?php

namespace Va_bulkfeaturemanager\Form\AdminProductsExtraConfiguration;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Va_bulkfeaturemanager\Entity\UnitFeatureProduct;
use Va_bulkfeaturemanager\Entity\UnitFeatureValue;
use Va_bulkfeaturemanager\Repository\UnitFeatureProductRepository;
use Va_bulkfeaturemanager\Repository\UnitFeatureRepository;
use Va_bulkfeaturemanager\Repository\UnitFeatureValueRepository;
class UnitFeatureProductsExtraDataHandler implements FormDataHandlerInterface
{
    private $unitFeatureProductRepository;
    private $unitFeatureRepository;
    private $unitFeatureValueRepository;
    private $entityManager;

    public function __construct(
        UnitFeatureProductRepository $unitFeatureProductRepository,
        UnitFeatureRepository $unitFeatureRepository,
        UnitFeatureValueRepository $unitFeatureValueRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->unitFeatureProductRepository = $unitFeatureProductRepository;
        $this->unitFeatureRepository = $unitFeatureRepository;
        $this->unitFeatureValueRepository = $unitFeatureValueRepository;
        $this->entityManager = $entityManager;
    }

    public function create(array $data)
    {
        $unitFeatureProduct = new UnitFeatureProduct();
        $unitFeatureProduct->setIdProductAttribute($data['id_product']);

        $unitFeature = $this->unitFeatureRepository->findOneById((int) $data['feature_id']);
        $unitFeatureValue = $this->unitFeatureValueRepository->findOneById((int) $data['feature_id_val']);

        $unitFeatureProduct->setUnitFeature($unitFeature);
        $unitFeatureProduct->setUnitFeatureValue($unitFeatureValue);

        $this->entityManager->persist($unitFeatureProduct);
        $this->entityManager->flush();

        return true;
    }

    public function update($id, array $data)
    {

        $unitFeatureProduct = $this->unitFeatureProductRepository->findOneBy(['idProductAttribute' => $id]);
        if($unitFeatureProduct === null){
            $data['id_product'] = $id;
            $this->create($data);
        }else{
            $unitFeature = $this->unitFeatureRepository->findOneById((int) $data['feature_id']);
            $unitFeatureValue = $this->unitFeatureValueRepository->findOneById((int) $data['feature_id_val']);

            $unitFeatureProduct->setUnitFeature($unitFeature);
            $unitFeatureProduct->setUnitFeatureValue($unitFeatureValue);

            $this->entityManager->persist($unitFeatureProduct);
            $this->entityManager->flush();
        }
        return true;
    }
}
