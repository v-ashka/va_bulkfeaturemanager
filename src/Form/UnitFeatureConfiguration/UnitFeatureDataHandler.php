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

    public function create(array $data)
    {
        $unitFeature = new UnitFeature();
        $unitFeature->setUnitFeatureName($data['unit_feature_name']);
        $unitFeature->setUnitFeatureShortcut($data['unit_feature_shortcut']);

        $this->entityManager->persist($unitFeature);
        $this->entityManager->flush();

        return $unitFeature->getId();
    }

    public function update($id, array $data)
    {
        $unitFeature = $this->unitFeatureRepository->findOneById($id);
        $unitFeature->setUnitFeatureName($data['unit_feature_name']);
        $unitFeature->setUnitFeatureShortcut($data['unit_feature_shortcut']);

        $this->entityManager->persist($unitFeature);
        $this->entityManager->flush();

        return $unitFeature->getId();
    }

}
