<?php

namespace Va_bulkfeaturemanager\Form\UnitFeatureValueConfiguration;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use Va_bulkfeaturemanager\Entity\UnitFeatureValue;
use Va_bulkfeaturemanager\Repository\UnitFeatureValueRepository;

class UnitFeatureValueDataHandler implements FormDataHandlerInterface
{
    private $unitFeatureValueRepository;
    private $entityManager;

    public function __construct(
        UnitFeatureValueRepository $unitFeatureValueRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->unitFeatureValueRepository = $unitFeatureValueRepository;
        $this->entityManager = $entityManager;
    }

    public function create(array $data)
    {
        $unitFeatureValue = new UnitFeatureValue();
        $unitFeatureValue->setValue($data['value']);


        $this->entityManager->persist($unitFeatureValue);
        $this->entityManager->flush();

        return $unitFeatureValue->getId();
    }

    public function update($idFeatureValue, array $data)
    {
        $unitFeatureValue = $this->unitFeatureValueRepository->findOneById($idFeatureValue);
        $unitFeatureValue->setValue($data['value']);
        $unitFeatureValue->setUnitFeature($idFeatureValue);
        $this->entityManager->persist($unitFeatureValue);
        $this->entityManager->flush();

        return $unitFeatureValue->getId();
    }
}
