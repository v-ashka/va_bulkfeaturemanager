<?php

declare(strict_types=1);

namespace Va_BulkFeatureManager\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
class BulkFeatureManagerController extends FrameworkBundleAdminController{

    public function indexAction(Request $request){
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/index.html.twig', [
            'form' => $this->createForm()
        ]);
    }
}
