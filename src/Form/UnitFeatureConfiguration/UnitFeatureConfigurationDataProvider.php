<?php

namespace Va_bulkfeaturemanager\Form\UnitFeatureConfiguration;

use Va_bulkfeaturemanager\Entity\UnitFeature;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use Va_bulkfeaturemanager\Repository\UnitFeatureRepository;

class UnitFeatureConfigurationDataProvider implements FormDataProviderInterface
{
    private $repository;

    public function __construct(UnitFeatureRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getData($featureId)
    {
        $feature = $this->repository->findOneById($featureId);


    }

    public function getDefaultData()
    {
        // TODO: Implement getDefaultData() method.
    }
}
