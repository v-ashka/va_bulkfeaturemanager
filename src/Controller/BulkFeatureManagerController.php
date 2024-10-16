<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Va_bulkfeaturemanager\Grid\Filters\BulkFeatureManagerFilters;

class BulkFeatureManagerController extends FrameworkBundleAdminController{

    public function indexAction(BulkFeatureManagerFilters $filters, Request $request){

//        dump($request);
        $res = $this->get('prestashop.module.va_bulkfeaturemanager.form.bulkfeaturemanager_data_handler');
        $grid = $this->get('prestashop.module.va_bulkfeaturemanager.grid.factory.bulkfeaturemanager');

//        dump($res->getForm());
//        dd($grid->getGrid($filters));
        $resForm = $res->getForm();
        $featureProductsGrid = $grid->getGrid($filters);
        $resForm->handleRequest($request);
//        dump($resForm);
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/index.html.twig', [
            'bulkFeatureManagerForm' => $resForm->createView(),
            'featureProductsGrid' => $this->presentGrid($featureProductsGrid)

        ]);
    }
}
