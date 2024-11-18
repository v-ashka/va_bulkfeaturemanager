<?php

declare(strict_types=1);

namespace Va_bulkfeaturemanager\Controller;

use PrestaShop\PrestaShop\Core\Foundation\Database\EntityNotFoundException;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Va_bulkfeaturemanager\Entity\UnitFeature;
use Va_bulkfeaturemanager\Grid\GridFeatureList\Definition\Factory\UnitFeatureDefinitionFactory;
use Va_bulkfeaturemanager\Grid\GridFeatureList\Filters\UnitFeatureFilters;
use Va_bulkfeaturemanager\Grid\GridFeatureValueList\Definition\Factory\GridFeatureValueDefinitionFactory;
use Va_bulkfeaturemanager\Grid\GridFeatureValueList\Filters\GridFeatureValueFilters;
use Va_bulkfeaturemanager\Grid\GridProductsFeature\Factory\BulkFeatureManagerGridDefinitionFactory;
use Va_bulkfeaturemanager\Grid\GridProductsFeature\Filters\BulkFeatureManagerFilters;
use Va_bulkfeaturemanager\Repository\UnitFeatureRepository;

class BulkFeatureManagerController extends FrameworkBundleAdminController{

    public function displayProductConfiguration(BulkFeatureManagerFilters $filters, Request $request){
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
                                            'Cannot add feature id: %featureName% to product id: %productId% because it has already added any feature ',
                                            'Modules.Va_bulkfeaturemanager.Admin',
                                            [
                                                '%featureName%' => $formResponse['feature_form']['feature_id'],
                                                '%productId%' => $warningProductId
                                            ]
                                        ));
                                    }

                                }
                            $this->addFlash('success', $this->trans(
                                'Feature id: %featureName% has been added to %productsCount% product/s',
                                'Modules.Va_bulkfeaturemanager.Admin',
                                [
                                    '%featureName%' => $formResponse['feature_form']['feature_id'],
                                    '%productsCount%' => isset($transactionInfo['warning']) ? ( (int) (count($formResponse['grid_id']) - count($transactionInfo['warning']))) : (count($formResponse['grid_id']))
                                ]
                            ));
                        }else{
                            $this->addFlash('error',
                                $this->trans(
                                    'The selected products already have any feature',
                                    'Modules.Va_bulkfeaturemanager.Admin')
                            );
                        }
                        break;
                    case 'remove_feature':
                        $transactionInfo = $bulkFeatureManagerService->removeFeatureFromProducts(
                            $formResponse['feature_form']['feature_id'],
                            $formResponse['grid_id']
                        );
                        if(isset($transactionInfo['success'])){
                            if(isset($transactionInfo['warning'])){
                                foreach($transactionInfo['warning'] as $warningProductId){
                                    $this->addFlash('warning', $this->trans(
                                        'Cannot remove feature id: %featureName% because it does not exist in product with id: %productId%',
                                        'Modules.Va_bulkfeaturemanager.Admin',
                                        [
                                            '%featureName%' => $formResponse['feature_form']['feature_id'],
                                            '%productId%' => $warningProductId
                                        ]
                                    ));
                                }

                            }

                            $this->addFlash('success', $this->trans(
                                'Removed feature id: %featureName% from %productsCount% product/s',
                                'Modules.Va_bulkfeaturemanager.Admin',
                                [
                                    '%featureName%' => $formResponse['feature_form']['feature_id'],
                                    '%productsCount%' => count($transactionInfo['success'])
                                ]
                            ));
                        }else{
                            $this->addFlash('error',
                                $this->trans(
                                    'The selected products does not have this feature',
                                    'Modules.Va_bulkfeaturemanager.Admin')
                            );
                        }
                        break;

                    case 'delete_all':
                        $transactionInfo = $bulkFeatureManagerService->removeAllFeaturesFromProducts(
                            $formResponse['grid_id']
                        );
                        if(isset($transactionInfo['success'])){

                            foreach($transactionInfo['success'] as $infoProductId){
                                $this->addFlash('info', $this->trans(
                                    'Deleted all features from product id: %productId%',
                                    'Modules.Va_bulkfeaturemanager.Admin',
                                    [

                                        '%productId%' => $infoProductId
                                    ]
                                ));
                            }

                            $this->addFlash('success', $this->trans(
                                'Removed all features from %productsCount% product/s',
                                'Modules.Va_bulkfeaturemanager.Admin',
                                [
                                    '%productsCount%' => count($transactionInfo['success'])
                                ]
                            ));
                        }

                        break;
                }
            }else{
                $this->addFlash('error',
                    $this->trans(
                        'Please select products before add or delete actions',
                        'Modules.Va_bulkfeaturemanager.Admin')
                );
            }

            return $this->redirectToRoute('va_bulkfeaturemanager');
        }

        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/index.html.twig', [
            'layoutTitle' => $this->trans('Bulk Feature Manager', 'Modules.Va_bulkfeaturemanager.Admin'),

            'bulkFeatureManagerForm' => $resForm->createView(),
            'gridForm' => $this->presentGrid($featureProductsGrid)

        ]);
    }

    public function configurationSearchGrid(Request $request){
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.module.va_bulkfeaturemanager.grid.bulkfeaturemanager.grid_definition_factory'),
            $request,
            BulkFeatureManagerGridDefinitionFactory::GRID_ID,
            'va_bulkfeaturemanager'
        );
    }

    public function indexAction(UnitFeatureFilters $filters)
    {
        $gridFeatureListFactory = $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_list.factory');
        $gridFeatureList = $gridFeatureListFactory->getGrid($filters);
            dump($gridFeatureList);
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/va_featureList.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Unit Feature Configuration', 'Modules.Va_bulkfeaturemanager.Admin'),
            'gridForm' => $this->presentGrid($gridFeatureList),

        ]);
    }

    public function searchAction(Request $request){
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');
//        dd($responseBuilder);
        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_list.definition.factory.unit_feature_definition_factory'),
            $request,
            UnitFeatureDefinitionFactory::GRID_ID,
            'va_bulkfeaturemanager_features_list'
        );
    }




    public function editFeatureAction(Request $request, int $featureId){
        $unitFeatureBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_configuration.builder.formbuilder');
        $unitFeatureForm = $unitFeatureBuilder->getFormFor($featureId);
        $unitFeatureForm->handleRequest($request);
            dump($unitFeatureForm);
            dump($request);
        $unitFeatureFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_configuration.form_handler');
        $result = $unitFeatureFormHandler->handleFor($featureId, $unitFeatureForm);

        if($result->isSubmitted() && $result->isValid()){
            $this->addFlash('success', $this->trans('Successful update', 'Admin.Va_bulkfeaturemanager.Success'));

            return $this->redirectToRoute('va_bulkfeaturemanager_features_list');
        }

        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/form/feature/feature-form.html.twig', [
            'feature_form' => $unitFeatureForm->createView(),
        ]);
    }


    public function addNewFeatureAction(Request $request){
        $unitFeatureBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_configuration.builder.formbuilder');
        $featureForm = $unitFeatureBuilder->getForm();
        $featureForm->handleRequest($request);

        $featureFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_configuration.form_handler');

        $result = $featureFormHandler->handle($featureForm);

        if(null !== $result->getIdentifiableObjectId()){
            $this->addFlash('success', $this->trans('A new feature has been successfully created', 'Admin.Va_bulkfeaturemanager.Feature'));

            return $this->redirectToRoute('va_bulkfeaturemanager_features_list');
        }

        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/form/feature/feature-form.html.twig', [
            'enableSidebar' => true,
            'feature_form' => $featureForm->createView(),
        ]);

    }

    public function deleteFeatureAction(int $featureId){
        $repository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_configuration_repository');

        try{
            $feature = $repository->findOneById($featureId);
        } catch (EntityNotFoundException $e){
            $feature = null;
        }

        if(null !== $feature){
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $entityManager->remove($feature);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Feature has been successfully deleted', 'Admin.Va_bulkfeaturemanager.Feature'));

        }else{
            $this->addFlash('error', $this->trans('This feature does not exist', 'Admin.Va_bulkfeaturemanager.Feature'));
        }

        return $this->redirectToRoute('va_bulkfeaturemanager_features_list');
    }



    public function displayFeatureValuesAction(Request $request, GridFeatureValueFilters $filters)
    {
        $featureValueGridFactory = $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_value_list.factory');
        $queryBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_value_list.query.gridfeaturevaluequerybuilder');
//        get feature id from featureForm request
        $featureId = $request->attributes->get('featureId');
//        set feature id to display exact feature values
        $queryBuilder->setFeatureId((int) $featureId);

        $featureValueDefinitionFactory = $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_value_list.definition.factory.gridfeaturevaluedefinitionfactory');

        $featureValueDefinitionFactory->setFeatureId($featureId);
        $featureValueGrid = $featureValueGridFactory->getGrid($filters);
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/va_featureList.html.twig', [
           'gridForm' => $this->presentGrid($featureValueGrid),
           'layoutTitle' => $this->trans('Feature %feature_name% configuration', 'Modules.Va_bulkfeaturemanager.Admin', ['%feature_name%' => 'test']),
        ]);
    }

    // search feature value action
    public function searchFeatureValueAction(Request $request){
//        dd($request->attributes);
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');
//        dd($responseBuilder, (int) $request->attributes->get('featureId'), $request->query);

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_value_list.definition.factory.gridfeaturevaluedefinitionfactory'),
            $request,
            GridFeatureValueDefinitionFactory::GRID_ID,
           'va_bulkfeaturemanager_feature_values',
            ['featureId']
        );
    }

    public function addNewFeatureValueAction(Request $request){
        $unitFeatureValueBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_value_configuration.builder.formbuilder');
        $featureValueForm = $unitFeatureValueBuilder->getForm();
        $featureValueForm->handleRequest($request);


        $featureValueFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_value_configuration.form_handler');
        $result = $featureValueFormHandler->handle($featureValueForm);

        if($result->isValid() && $result->isSubmitted() && $result->getIdentifiableObjectId() !== null ){
            $featureName = $this->getFeatureName($featureValueForm->getData()['unit_feature_id']);

            $this->addFlash('success',  $this->trans('A new feature value has been successfully created (Added to feature named: %feature_name% id: %feature_id%)', 'Admin.Va_bulkfeaturemanager.Feature', ['%feature_id%' => $featureValueForm->getData()['unit_feature_id'], '%feature_name%' => $featureName]));
            return $this->redirectToRoute('va_bulkfeaturemanager_features_list');
        }
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/form/feature/feature-form.html.twig', [
            'enableSidebar' => true,
            'feature_form' => $featureValueForm->createView(),
        ]);
    }

    public function getFeatureName($featureId){
        $featureRepository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_configuration_repository');
        $feature = $featureRepository->findOneById($featureId);
        return $feature->getUnitFeatureName() !== null ? $feature->getUnitFeatureName() : $this->trans('Feature name not found', 'Admin.Va_bulkfeaturemanager.FeatureValueConfiguration');

    }
    public function editFeatureValueAction(Request $request, int $featureValueId)
    {
        $unitFeatureValueBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_value_configuration.builder.formbuilder');
        $unitFeatureValueForm = $unitFeatureValueBuilder->getFormFor($featureValueId);

        $unitFeatureValueForm->handleRequest($request);

        $unitFeatureValueFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_value_configuration.form_handler');
        $result = $unitFeatureValueFormHandler->handleFor($featureValueId, $unitFeatureValueForm);

        if($result->isSubmitted() && $result->isValid()){
            $this->addFlash('success', $this->trans('Successful update', 'Admin.Va_bulkfeaturemanager.Success'));

            return $this->redirectToRoute('va_bulkfeaturemanager_feature_values', ['featureId' => $unitFeatureValueForm->getData()['unit_feature_id']]);
        }

        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/form/feature/feature-form.html.twig', [
            'feature_form' => $unitFeatureValueForm->createView(),
        ]);
    }

    public function deleteFeatureValue(int $featureValueId)
    {
        $repository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_value_configuration_repository');
        try{
            $feature = $repository->findOneById($featureValueId);
        } catch (EntityNotFoundException $e){
            $feature = null;
        }

        if(null !== $feature){
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $entityManager->remove($feature);
            $entityManager->flush();

            $this->addFlash('success', $this->trans('Feature value has been successfully deleted', 'Admin.Va_bulkfeaturemanager.Feature'));

        }else{
            $this->addFlash('error', $this->trans('This feature value does not exist', 'Admin.Va_bulkfeaturemanager.Feature'));
        }

        return $this->redirectToRoute('va_bulkfeaturemanager_feature_values', ['featureId' => $feature->getUnitFeature()->getId()]);

    }

    public function deleteBulkFeatureValue(Request $request){
        $featureValueIds = $request->request->get('gridFeatureValueList_bulk');
        $repository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_value_configuration_repository');

        try{
            $featuresValue = $repository->findById($featureValueIds);
        } catch (EntityNotFoundException $e){
            $featuresValue = null;
        }

        if(!empty($featuresValue)){
            $em = $this->get('doctrine.orm.entity_manager');
            foreach($featuresValue as $featureValueId){

                $em->remove($featureValueId);
            }
            $em->flush();
            $this->addFlash('success', $this->trans('Successful delete', 'Admin.Va_bulkfeaturemanager.Feature'));
        }

        return $this->redirectToRoute('va_bulkfeaturemanager_feature_values', ['featureId' => $feature->getUnitFeature()->getId()]);
    }
}
