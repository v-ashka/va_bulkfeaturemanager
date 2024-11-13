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

    public function create(array $data)
    {
        $unitFeatureValue = new UnitFeatureValue();


        $unitFeatureValue->setValue($data['unit_feature_value']);

        $unitFeature = $this->unitFeatureRepository->findOneById($data['unit_feature_id']);

        $unitFeatureValue->setUnitFeature($unitFeature);
        $this->entityManager->persist($unitFeatureValue);
        $this->entityManager->flush();

        return $unitFeatureValue->getId();
    }

    public function update($id, array $data)
    {
        $unitFeatureValue = $this->unitFeatureValueRepository->findOneById($id);
        $unitFeatureValue->setValue($data['unit_feature_value']);

        $unitFeature = $this->unitFeatureRepository->findOneById($data['unit_feature_id']);
        $unitFeatureValue->setUnitFeature($unitFeature);

//        $unitFeatureValue->setUnitFeature($idFeatureValue);
        $this->entityManager->persist($unitFeatureValue);
        $this->entityManager->flush();

        return $unitFeatureValue->getId();
    }
}
