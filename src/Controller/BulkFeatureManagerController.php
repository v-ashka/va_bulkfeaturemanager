<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
class BulkFeatureManagerController extends FrameworkBundleAdminController{

    public function indexAction(Request $request){

//        dump($request);
        $res = $this->get('prestashop.module.va_bulkfeaturemanager.form.bulkfeaturemanager_data_handler');
        $grid = $this->get('prestashop.module.va_bulkfeaturemanager.grid.bulkfeaturemanager.grid_definition_factory');

//        dump($res->getForm());
        dd($grid);
        $resForm = $res->getForm();
        $resForm->handleRequest($request);
//        dump($resForm);
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/index.html.twig', [
            'bulkFeatureManagerForm' => $resForm->createView(),
        ]);
    }
}
