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
    private $unitFeatureRepository;
    private $entityManager;

    public function __construct(
        UnitFeatureProductRepository $unitFeatureRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->unitFeatureRepository = $unitFeatureRepository;
        $this->entityManager = $entityManager;
    }

    public function create(array $data)
    {
//        $unitFeatureProduct = new UnitFeatureProduct();
        $unitFeatureProduct = $this->unitFeatureRepository->findOneById($data['id_product']);
        $unitFeatureProduct->setIdProductAttribute($data['id_product']);
        dd($unitFeatureProduct);
    }

    public function update($id, array $data)
    {
        $unitFeatureProduct = $this->unitFeatureRepository->findOneById($data['id_product']);
        $unitFeatureProduct->setIdProductAttribute($data['id_product']);
        dd($unitFeatureProduct);

    }
}
