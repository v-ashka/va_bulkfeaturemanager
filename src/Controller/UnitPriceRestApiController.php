<?php
declare(strict_types=1);

namespace Va_bulkfeaturemanager\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Va_bulkfeaturemanager\Grid\GridProductsFeature\Filters\BulkFeatureManagerFilters;

class UnitPriceRestApiController extends FrameworkBundleAdminController
{

    public function getRouteLinks(Request $request){
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/index.html.twig', [
            'getFeaturesDataUrl' => $this->generateUrl('va_bulkfeaturemanager_get_features_data'),
            'getProductListDataUrl' => $this->generateUrl('va_bulkfeaturemanager_get_products_data'),
        ]);
    }

    public function getFeaturesData(Request $request)
    {
        // Get features and feature values
        $bulkFeatureManagerService = $this->get('prestashop.module.va_bulkfeaturemanager.service.bulkfeaturemanager_service');
        return $this->json($bulkFeatureManagerService);
    }

    public function getProductsList(BulkFeatureManagerFilters $filters){
        $grid = $this->get('prestashop.module.va_bulkfeaturemanager.grid.factory.bulkfeaturemanager');
        return $this->json($grid->getGrid($filters)->getData()->getRecords());
    }

}
