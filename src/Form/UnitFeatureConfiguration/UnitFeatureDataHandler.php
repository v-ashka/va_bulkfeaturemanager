<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Form\UnitFeatureConfiguration;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use Va_bulkfeaturemanager\Entity\UnitFeature;
use Va_bulkfeaturemanager\Repository\UnitFeatureRepository;

class UnitFeatureDataHandler implements FormDataHandlerInterface
{
    private $unitFeatureRepository;
    private $entityManager;

    public function __construct(
        UnitFeatureRepository $unitFeatureRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->unitFeatureRepository = $unitFeatureRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Add new feature
     * @param array $data
     * @return int|mixed
     */
    public function create(array $data)
    {
        $unitFeature = new UnitFeature();
//        Set feature name
        $unitFeature->setUnitFeatureName($data['unit_feature_name']);
//        Set feature shortcut
        $unitFeature->setUnitFeatureShortcut($data['unit_feature_shortcut']);
//        Set feature base value
        $unitFeature->setUnitFeatureBaseValue((int) $data['unit_feature_base_value']);

//        Save values to database
        $this->entityManager->persist($unitFeature);
        $this->entityManager->flush();
        return $unitFeature->getId();
    }

    /**
     *  Modify and update feature
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
//        Find exists feature record and set data
        $unitFeature = $this->unitFeatureRepository->findOneById($id);
        $unitFeature->setUnitFeatureName($data['unit_feature_name']);
        $unitFeature->setUnitFeatureShortcut($data['unit_feature_shortcut']);
        $unitFeature->setUnitFeatureBaseValue((int) $data['unit_feature_base_value']);

//        Save values to database
        $this->entityManager->persist($unitFeature);
        $this->entityManager->flush();
        return $unitFeature->getId();
    }

}
