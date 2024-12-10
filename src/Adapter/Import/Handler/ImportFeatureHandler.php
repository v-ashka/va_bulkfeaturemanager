<?php

namespace Va_bulkfeaturemanager\Adapter\Import\Handler;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShop\PrestaShop\Adapter\Entity\Product;
use PrestaShop\PrestaShop\Adapter\Import\Handler\AbstractImportHandler;
use PrestaShop\PrestaShop\Adapter\Import\ImportDataFormatter;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportRuntimeConfigInterface;
use PrestaShop\PrestaShop\Core\Import\Exception\InvalidDataRowException;
use PrestaShop\PrestaShop\Core\Import\Exception\SkippedIterationException;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Va_bulkfeaturemanager\Entity\UnitFeature;
use Va_bulkfeaturemanager\Entity\UnitFeatureProduct;
use Va_bulkfeaturemanager\Entity\UnitFeatureValue;

class ImportFeatureHandler extends AbstractImportHandler
{
    protected $entityManager;
    protected $productRepository;
    public function __construct(
        ImportDataFormatter $dataFormatter,
        array $allShopIds,
        array $contextShopIds,
        $currentContextShopId,
        $isMultistoreEnabled,
        $contextLanguageId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        $employeeId,
        Database $legacyDatabase,
        CacheClearerInterface $cacheClearer,
        Configuration $configuration,
        Validate $validate,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
        )
    {
        parent::__construct(
            $dataFormatter,
            $allShopIds,
            $contextShopIds,
            $currentContextShopId,
            $isMultistoreEnabled,
            $contextLanguageId,
            $translator,
            $logger,
            $employeeId,
            $legacyDatabase,
            $cacheClearer,
            $configuration,
            $validate
        );

        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;

    }

    public function importRow(
        ImportConfigInterface $importConfig,
        ImportRuntimeConfigInterface $runtimeConfig,
        DataRowInterface $dataRow
    )
    {
        parent::importRow($importConfig, $runtimeConfig, $dataRow);
        $arr = $runtimeConfig->getEntityFields();
        $importType = end($arr)['import_type'];
        $forceIds = $importConfig->forceIds();
        $entity = null;
        $unitFeatureValueRepository = $this->entityManager->getRepository(UnitFeatureValue::class);
        $unitFeatureRepository = $this->entityManager->getRepository(UnitFeature::class);
//        $unitFeatureProductRepository = $this->entityManager->getRepository(UnitFeatureProduct::class);

        switch ($importType) {
            case 'features':
                $id = (int) $this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'id_unit_feature');
                if ($forceIds && $id > 0) {
                    // Find exists entity by id
                    $entity = $unitFeatureRepository->findOneById($id);
                }

                // If entity not exists, create new one
                if ($entity === null) {
                    $entity = new UnitFeature();
                    if ($forceIds) {
                        $entity->setId($id);
                    }
                }
                // Set new values for entity
                $entity->setUnitFeatureName($this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'unit_feature_name'));
                $entity->setUnitFeatureShortcut($this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'unit_feature_shortcut'));
                $entity->setUnitFeatureBaseValue($this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'unit_feature_base_value'));
                break;
            case 'feature_values':
                $id = (int) $this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'id_unit_feature_value');
                $unitFeatureId = $unitFeatureRepository->findOneById($this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'id_unit_feature'));

                if ($unitFeatureId !== null) {
                    if ($forceIds && $id > 0) {
                        // Find exists entity by id
                        $entity = $unitFeatureValueRepository->findOneById($id);
                    }
                    // If entity not exists, create new one
                    if ($entity === null) {
                        $entity = new UnitFeatureValue();
                        if ($forceIds) {
                            $entity->setId($id);
                        }
                    }
                    // Set new values for entity

                    $entity->setUnitFeature($unitFeatureId);
                    $entity->setValue($this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'value'));
                }
                break;
            case 'feature_product':
                $id = (int) $this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'id');
                $unitFeatureId = $unitFeatureRepository->findOneById($this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'id_unit_feature'));
                $unitFeatureValueId = $unitFeatureValueRepository->findOneById($this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'id_unit_feature_value'));
                $productId = new Product($this->fetchDataValueByKey($dataRow, $runtimeConfig->getEntityFields(), 'id_product'));

                if ($unitFeatureId !== null && $unitFeatureValueId !== null && $productId->id !== null) {
                    if ($forceIds && $id > 0) {
                        // Find exists entity by id
                        $entity = $this->entityManager->getRepository(UnitFeatureProduct::class)->findOneById($id);
                    }

                    // If entity not exists, create new one
                    if ($entity === null) {
                        $entity = new UnitFeatureProduct();
                        if ($forceIds) {
                            $entity->setId($id);
                        }
                    }
                    // Set new values for entity
                    $entity->setUnitFeatureValue($unitFeatureValueId->getId());
                    $entity->setUnitFeature($unitFeatureId->getId());
                    $entity->setIdProductAttribute($productId->id);
//                    $runtimeConfig->setNumberOfProcessedRows(1);
                }
                break;
        }

        if($entity != null && !$runtimeConfig->shouldValidateData()){
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
    }

    public function supports($importEntityType)
    {
        return $importEntityType === 'features';
    }

    public function tearDown(ImportConfigInterface $importConfig, ImportRuntimeConfigInterface $runtimeConfig){
         parent::tearDown($importConfig, $runtimeConfig);
    }
}
