<?php

namespace Va_bulkfeaturemanager\Form\AdminProductsExtraConfiguration;

use phpDocumentor\Reflection\Types\Boolean;
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

    /**
     * Attach features to new product
     * @param array $data
     * @return bool
     */
    public function create(array $data): bool
    {
        $unitFeatureProduct = new UnitFeatureProduct();
//        Set product id
        $unitFeatureProduct->setIdProductAttribute($data['id_product']);

//        Compare if unit feature and unit feature value exists
        $unitFeature = $this->unitFeatureRepository->findOneById((int) $data['feature_id']);
        $unitFeatureValue = $this->unitFeatureValueRepository->findOneById((int) $data['feature_id_val']);

        if($unitFeature == null || $unitFeatureValue == null){
            return false;
        }

//        Set unit feature and unit feature value
        $unitFeatureProduct->setUnitFeature($unitFeature);
        $unitFeatureProduct->setUnitFeatureValue($unitFeatureValue);

//        Save data to database using doctrine entity manager
        $this->entityManager->persist($unitFeatureProduct);
        $this->entityManager->flush();
        return true;
    }

    /**
     * Modify feature in product
     * @param $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
//        Find if this product id have attached any features
        $unitFeatureProduct = $this->unitFeatureProductRepository->findOneBy(['idProductAttribute' => $id]);
        if($unitFeatureProduct === null){
//            If not alter data array and do create() method
            $data['id_product'] = $id;
            $this->create($data);
        }else{
//            Find if passed data values exists in db
            $unitFeature = $this->unitFeatureRepository->findOneById((int) $data['feature_id']);
            $unitFeatureValue = $this->unitFeatureValueRepository->findOneById((int) $data['feature_id_val']);
            if($unitFeature == null || $unitFeatureValue === null){
                return false;
            }

//            Set unit feature and feature value
            $unitFeatureProduct->setUnitFeature($unitFeature);
            $unitFeatureProduct->setUnitFeatureValue($unitFeatureValue);

//        Save data to database using doctrine entity manager
            $this->entityManager->persist($unitFeatureProduct);
            $this->entityManager->flush();
        }
        return true;
    }
}
