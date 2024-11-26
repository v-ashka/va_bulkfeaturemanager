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

    /**
     * Get feature value data, by providing featureValueId
     * @param $featureValueId
     * @return array
     */
    public function getData($featureValueId): array
    {
//        Find if feature value exists with that id
       $featureValue = $this->featureValueRepository->findOneById($featureValueId);
       return
       [
           'unit_feature_id' => $featureValue->getUnitFeature()->getId(),
           'unit_feature_value' => $featureValue->getValue(),
       ];
    }

    /**
     * Get default value
     * @return string[]
     */
    public function getDefaultData()
    {
        return [
                'unit_feature_id' => '',
                'unit_feature_value' => ''
            ];
    }
}
