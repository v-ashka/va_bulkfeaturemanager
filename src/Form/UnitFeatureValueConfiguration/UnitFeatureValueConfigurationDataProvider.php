<?php

namespace Va_bulkfeaturemanager\Form\UnitFeatureValueConfiguration;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use Va_bulkfeaturemanager\Repository\UnitFeatureValueRepository;

class UnitFeatureValueConfigurationDataProvider implements FormDataProviderInterface
{
    private $featureValueRepository;

    public function __construct(UnitFeatureValueRepository $featureValueRepository)
    {
        $this->featureValueRepository = $featureValueRepository;
    }


    public function getData($featureValueId): array
    {
       $featureValue = $this->featureValueRepository->findOneById($featureValueId);

       return
       [
           'unit_feature_id' => $featureValue->getUnitFeature(),
           'unit_feature_value' => $featureValue->getValue(),
       ];
    }

    public function getDefaultData()
    {
        return [
                'unit_feature_id' => '',
                'unit_feature_value' => ''
            ];
    }
}
