<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
class BulkFeatureManagerController extends FrameworkBundleAdminController{

    public function indexAction(Request $request){
        $rep = $this->get('prestashop.module.va_bulkfeaturemanager.form.formbuilder');
        dd($rep);
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/index.html.twig', [
        ]);
    }
}
