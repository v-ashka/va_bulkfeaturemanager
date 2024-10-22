<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Controller;

use Doctrine\DBAL\Connection;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Feature\ProductFeature;
use Symfony\Component\HttpFoundation\Request;
use Va_bulkfeaturemanager\Grid\Filters\BulkFeatureManagerFilters;
use Va_bulkfeaturemanager\Service\BulkFeatureManagerService;


class BulkFeatureManagerController extends FrameworkBundleAdminController{

    public function indexAction(BulkFeatureManagerFilters $filters, Request $request){
        $res = $this->get('prestashop.module.va_bulkfeaturemanager.form.bulkfeaturemanager_data_handler');
        $grid = $this->get('prestashop.module.va_bulkfeaturemanager.grid.factory.bulkfeaturemanager');
        $resForm = $res->getForm();
        $featureProductsGrid = $grid->getGrid($filters);
        $resForm->handleRequest($request);
        $formResponse = [];

        $bulkFeatureManagerService = $this->get('prestashop.module.va_bulkfeaturemanager.service.bulkfeaturemanager_service');
        if($resForm->isSubmitted() && $resForm->isValid()){
            $formResponse['feature_form'] = $resForm->getData();
            $formResponse['grid_id'] = $request->request->get('bulkFeatureManagerGrid_bulk');
            if($formResponse['grid_id'] !== null){
                switch ($formResponse['feature_form']['feature_method']){
                    case 'add_feature':
                        $transactionInfo = $bulkFeatureManagerService->addFeaturesToProducts(
                            $formResponse['feature_form']['feature_id'],
                            $formResponse['feature_form']['feature_id_val'],
                            $formResponse['grid_id']
                        );
                        if(isset($transactionInfo['success'])){
                            // for catch duplicate feature in products
                            if(isset($transactionInfo['warning'])){
                                    foreach($transactionInfo['warning'] as $warningProductId){
                                        $this->addFlash('warning', $this->trans(
                                            'Cannot add feature id: %featureName% because it has already been added to product id: %productId%',
                                            'Modules.Va_bulkfeaturemanager.Admin',
                                            [
                                                '%featureName%' => $formResponse['feature_form']['feature_id'],
                                                '%productId%' => $warningProductId
                                            ]
                                        ));
                                    }

                                }
                            $this->addFlash('success', $this->trans(
                                'Feature id: %featureName% has been added to %productsCount% products',
                                'Modules.Va_bulkfeaturemanager.Admin',
                                [
                                    '%featureName%' => $formResponse['feature_form']['feature_id'],
                                    '%productsCount%' => count($formResponse['grid_id'])
                                ]
                            ));
                        }else{
                            $this->addFlash('error',
                                $this->trans(
                                    'The selected products already have this feature',
                                    'Modules.Va_bulkfeaturemanager.Admin')
                            );
                        }
                        break;
                    case 'remove_feature':
                        //
                        break;

                    case 'delete_all':
                        //
                        break;
                }
            }else{
                $this->addFlash('error',
                    $this->trans(
                        'Please select products before add or delete features',
                        'Modules.Va_bulkfeaturemanager.Admin')
                );
            }


        }

        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/index.html.twig', [
            'bulkFeatureManagerForm' => $resForm->createView(),
            'featureProductsGrid' => $this->presentGrid($featureProductsGrid)

        ]);
    }

}
