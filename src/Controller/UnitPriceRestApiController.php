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
            'getProductListDataUrl' => $this->generateUrl('va_bulkfeaturemanager_get_products_data', ['productsLimit' => 0]),
        ]);
    }

    public function getFeaturesData(Request $request)
    {
        // Get features and feature values
        $bulkFeatureManagerService = $this->get('prestashop.module.va_bulkfeaturemanager.service.bulkfeaturemanager_service');
        return $this->json($bulkFeatureManagerService);
    }

    public function getProductsList(Request $request, int $productsLimit, BulkFeatureManagerFilters $filters){
        if ($productsLimit == null){
            $productsLimit = (int)$request->get('limit', $filters->getLimit());
        }
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        dump($request->attributes->all());
        dump($request->query->all());
        dump($responseBuilder);
        dump($responseBuilder);
        // Build search response
        dd($responseBuilder->buildSearchResponse(
            $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_list.definition.factory.unit_feature_definition_factory'),
            $request,
            $filters->getFilterId(),
            'va_bulkfeaturemanager_get_features_data'
        ));


        $grid = $this->get('prestashop.module.va_bulkfeaturemanager.grid.factory.bulkfeaturemanager');
        $filters->set('limit', $productsLimit);
//        dd($filters);
        $records = $grid->getGrid($filters)->getData()->getRecords();
        return $this->json(
            [
                'records' => $records,
                'totalRecords' => $grid->getGrid($filters)->getData()->getRecordsTotal(),
            ]
       );
    }
}
