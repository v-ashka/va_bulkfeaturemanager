<?php

declare(strict_types=1);

namespace Va_BulkFeatureManager\Form;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Va_BulkFeatureManager\Repository\BulkFeatureManagerRepository;

final class BulkFeatureManagerFormDataProvider implements FormDataProviderInterface{

    private $repository;

    public function __construct(BulkFeatureManagerRepository $repository)
    {
        $this->repository = $repository;
    }


    public function getData()
    {
        return [
            'productsIds' => [],
            'id_feature_value' => '',
            'id_feature' => ''
        ];
    }

    public function setData(array $data)
    {
        // TODO: Implement setData() method.
    }
}
