<?php

namespace Va_bulkfeaturemanager\Form\UnitFeatureValueConfiguration;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use Va_bulkfeaturemanager\Entity\UnitFeatureValue;
use Va_bulkfeaturemanager\Repository\UnitFeatureRepository;
use Va_bulkfeaturemanager\Repository\UnitFeatureValueRepository;

class UnitFeatureValueDataHandler implements FormDataHandlerInterface
{
    private $unitFeatureValueRepository;
    private $unitFeatureRepository;
    private $entityManager;

    public function __construct(
        UnitFeatureValueRepository $unitFeatureValueRepository,
        UnitFeatureRepository $unitFeatureRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->unitFeatureValueRepository = $unitFeatureValueRepository;
        $this->unitFeatureRepository = $unitFeatureRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Create new feature value to specified feature
     * @param array $data
     * @return int|mixed
     */
    public function create(array $data)
    {
        $unitFeatureValue = new UnitFeatureValue();
        $unitFeatureValue->setValue($data['unit_feature_value']);

//      Find existing feature by unit_feature_id
        $unitFeature = $this->unitFeatureRepository->findOneById($data['unit_feature_id']);
        $unitFeatureValue->setUnitFeature($unitFeature);

//        Save data to database
        $this->entityManager->persist($unitFeatureValue);
        $this->entityManager->flush();
        return $unitFeatureValue->getId();
    }

    /**
     * Modify and update feature value depend on featureId
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
//      Find feature value depends on feature value id, and set to new value
        $unitFeatureValue = $this->unitFeatureValueRepository->findOneById($id);
        $unitFeatureValue->setValue($data['unit_feature_value']);

//      Find feature depends on featureId, and update this field with new value.
        $unitFeature = $this->unitFeatureRepository->findOneById($data['unit_feature_id']);
        $unitFeatureValue->setUnitFeature($unitFeature);

//      Update data in database.
        $this->entityManager->persist($unitFeatureValue);
        $this->entityManager->flush();
        return $unitFeatureValue->getId();
    }
}
