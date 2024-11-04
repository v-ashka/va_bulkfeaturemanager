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

    //    get data and show them in form (edit)
    public function getData($featureId)
    {
        $feature = $this->repository->findOneById($featureId);
        $unitFeature = [
            'unit_feature_name' => $feature->getUnitFeatureName(),
            'unit_feature_shortcut' => $feature->getUnitFeatureShortcut(),
        ];


        return $unitFeature;
    }

    public function getDefaultData()
    {
        return [
            'unit_feature_name' => '',
            'unit_feature_shortcut' => '',
        ];
    }
}
