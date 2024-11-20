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
    }

    public function update($id, array $data)
    {

        $unitFeatureProduct = $this->unitFeatureProductRepository->findOneBy(['idProductAttribute' => $id]);
        file_put_contents(
            _PS_MODULE_DIR_ .'/va_bulkfeaturemanager/2debug_hook.log',
            json_encode([
                'time' => date('Y-m-d H:i:s'),
                'hook' => 'hookActionAdminProductsControllerSaveAfter',
                'id' => $id,
                'data' => $data,
                'repository_class' => get_class($this->unitFeatureProductRepository),
                'entity_manager_class' => get_class($this->entityManager),
                'repository_methods' => get_class_methods($this->unitFeatureProductRepository),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n",
            FILE_APPEND
        );
        $unitFeature = $this->unitFeatureRepository->findOneById((int) $data['feature_id']);
        $unitFeatureValue = $this->unitFeatureValueRepository->findOneById((int) $data['feature_id_val']);

        $unitFeatureProduct->setUnitFeature($unitFeature);
        $unitFeatureProduct->setUnitFeatureValue($unitFeatureValue);
//        dump($unitFeatureProduct);
            dump($unitFeatureProduct);
        $this->entityManager->persist($unitFeatureProduct);
        $this->entityManager->flush();
            return true;
    }
}
