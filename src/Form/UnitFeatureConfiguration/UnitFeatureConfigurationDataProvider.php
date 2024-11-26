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

    /**
     * Get data attached to specific featureId and show them in form while editing
     * @param $featureId
     * @return array
     */
    public function getData($featureId)
    {
//        Find feature by specific id
        $feature = $this->repository->findOneById($featureId);
//        Return array
        $unitFeature = [
            'unit_feature_name' => $feature->getUnitFeatureName(),
            'unit_feature_shortcut' => $feature->getUnitFeatureShortcut(),
            'unit_feature_base_value' => $feature->getUnitFeatureBaseValue(),
        ];
        return $unitFeature;
    }

    /**
     * Return default data in form
     * @return string[]
     */
    public function getDefaultData()
    {
        return [
            'unit_feature_name' => '',
            'unit_feature_shortcut' => '',
            'unit_feature_base_value' => '',
        ];
    }

}
