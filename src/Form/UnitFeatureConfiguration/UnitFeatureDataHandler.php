<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Form\UnitFeatureConfiguration;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
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
        // TODO: Implement create() method.
    }

    public function update($id, array $data)
    {
        // TODO: Implement update() method.
    }
}
