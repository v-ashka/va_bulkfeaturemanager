<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Form;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use Va_BulkFeatureManager\Repository\BulkFeatureManagerRepository;

class BulkFeatureManagerFormDataProvider implements FormDataProviderInterface{

    private $repository;

    public function __construct(BulkFeatureManagerRepository $repository)
    {
        $this->repository = $repository;
    }


    public function getData()
    {

        return [
        ];
    }

    public function setData(array $data)
    {

            return $data;
    }
}
