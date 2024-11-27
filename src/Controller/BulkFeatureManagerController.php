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

    /**
     * Main controller to manage features with products
     * @param BulkFeatureManagerFilters $filters
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function displayProductConfiguration(BulkFeatureManagerFilters $filters, Request $request){
        // Get data handler service and grid factory service
        $res = $this->get('prestashop.module.va_bulkfeaturemanager.form.bulkfeaturemanager_data_handler');
        $grid = $this->get('prestashop.module.va_bulkfeaturemanager.grid.factory.bulkfeaturemanager');
        $bulkFeatureManagerService = $this->get('prestashop.module.va_bulkfeaturemanager.service.bulkfeaturemanager_service');

        // get form data and handle request
        $resForm = $res->getForm();
        $resForm->handleRequest($request);
        // get grid with specific filters
        $featureProductsGrid = $grid->getGrid($filters);
        $formResponse = [];

        // Check if form is submitted and is valid
        if($resForm->isSubmitted() && $resForm->isValid()){
            // get data from "feature_form" form
            $formResponse['feature_form'] = $resForm->getData();
            // get selected ids from grid
            $formResponse['grid_id'] = $request->request->get('bulkFeatureManagerGrid_bulk');

            // if user selected product / products
            if($formResponse['grid_id'] !== null){
                // check which method was chosen
                switch ($formResponse['feature_form']['feature_method']){
                    // in add_feature method
                    case 'add_feature':
                        // add selected feature to product
                        $transactionInfo = $bulkFeatureManagerService->addFeaturesToProducts(
                            $formResponse['feature_form']['feature_id'],
                            $formResponse['feature_form']['feature_id_val'],
                            $formResponse['grid_id']
                        );
                        // if transaction log has "success"
                        if(isset($transactionInfo['success'])){
                            // display success flash
                            $this->addFlash('success', $this->trans(
                                'Feature id: %featureName% has been added to %productsCount% product/s',
                                'Modules.Va_bulkfeaturemanager.Admin',
                                [
                                    '%featureName%' => $formResponse['feature_form']['feature_id'],
                                    '%productsCount%' => isset($transactionInfo['warning']) ? ( (int) (count($formResponse['grid_id']) - count($transactionInfo['warning']))) : (count($formResponse['grid_id']))
                                ]
                            ));
                        }else{
                            // display warning flash
                            $this->addFlash('warning',
                                $this->trans(
                                    'Product property overwritten successfully',
                                    'Modules.Va_bulkfeaturemanager.Admin')
                            );
                        }
                        break;
                    // in remove feature action
                    case 'remove_feature':
                        // delete feature from products
                        $transactionInfo = $bulkFeatureManagerService->removeFeatureFromProducts(
                            $formResponse['feature_form']['feature_id'],
                            $formResponse['grid_id']
                        );
                        if(isset($transactionInfo['success'])){
                            // display success flash
                            $this->addFlash('success', $this->trans(
                                'Removed feature id: %featureName% from %productsCount% product/s',
                                'Modules.Va_bulkfeaturemanager.Admin',
                                [
                                    '%featureName%' => $formResponse['feature_form']['feature_id'],
                                    '%productsCount%' => count($transactionInfo['success'])
                                ]
                            ));
                        }else{
                            // display error flash
                            $this->addFlash('error',
                                $this->trans(
                                    'The selected product/s does not have any feature',
                                    'Modules.Va_bulkfeaturemanager.Admin')
                            );
                        }
                        break;

                    case 'delete_all':
                        // Delete all features from products
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
            // redirect to route
            return $this->redirectToRoute('va_bulkfeaturemanager');
        }

        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/index.html.twig', [
            'layoutTitle' => $this->trans('Unit Price Manager', 'Modules.Va_bulkfeaturemanager.Admin'),
            'bulkFeatureManagerForm' => $resForm->createView(),
            'gridForm' => $this->presentGrid($featureProductsGrid)

        ]);
    }

    /**
     * Display list grid with features
     * @param UnitFeatureFilters $filters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function featureListGridAction(UnitFeatureFilters $filters)
    {
        $gridFeatureListFactory = $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_list.factory');
        $gridFeatureList = $gridFeatureListFactory->getGrid($filters);

        // Render grid
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/va_featureList.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Feature values list', 'Modules.Va_bulkfeaturemanager.Admin'),
            'gridForm' => $this->presentGrid($gridFeatureList),

        ]);
    }

    /**
     * Search controller for featureListGridAction grid list
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function searchAction(Request $request){
        // get response builder service
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        // Build search response
        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_list.definition.factory.unit_feature_definition_factory'),
            $request,
            UnitFeatureDefinitionFactory::GRID_ID,
            'va_bulkfeaturemanager_features_list'
        );
    }

    /**
     * Edit controller for feature form
     * @param Request $request
     * @param int $featureId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editFeatureAction(Request $request, int $featureId){
        // get feature builder service
        $unitFeatureBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_configuration.builder.formbuilder');
        // get featureId values
        $unitFeatureForm = $unitFeatureBuilder->getFormFor($featureId);
        $unitFeatureForm->handleRequest($request);

        // get form handler service
        $unitFeatureFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_configuration.form_handler');
        $result = $unitFeatureFormHandler->handleFor($featureId, $unitFeatureForm);

        // if form is submitted and valid
        if($result->isSubmitted() && $result->isValid()){
            // add success flash message
            $this->addFlash('success', $this->trans('Successful update', 'Admin.Va_bulkfeaturemanager.Success'));
            return $this->redirectToRoute('va_bulkfeaturemanager_features_list');
        }

        // render edit form
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

    /**
     *
     * Delete feature from database
     * @param int $featureId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
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


    /**
     * Display list grid with features values
     * @param Request $request
     * @param GridFeatureValueFilters $filters
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function displayFeatureValuesAction(Request $request, GridFeatureValueFilters $filters)
    {
        // get grid services
        $featureValueGridFactory = $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_value_list.factory');
        $queryBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_value_list.query.gridfeaturevaluequerybuilder');
        $featureValueDefinitionFactory = $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_value_list.definition.factory.gridfeaturevaluedefinitionfactory');

//        get feature id from featureForm request
        $featureId = $request->attributes->get('featureId');
//        set feature id to display exact feature values
        $queryBuilder->setFeatureId((int) $featureId);
        $featureValueDefinitionFactory->setFeatureId($featureId);
        // set filters to display grid
        $featureValueGrid = $featureValueGridFactory->getGrid($filters);
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/va_featureList.html.twig', [
           'gridForm' => $this->presentGrid($featureValueGrid),
           'layoutTitle' => $this->trans('Feature %feature_name% configuration', 'Modules.Va_bulkfeaturemanager.Admin', ['%feature_name%' => 'test']),
        ]);
    }

    /**
     * Search controller for featureValueListGridAction grid
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function searchFeatureValueAction(Request $request){
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');
        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.module.va_bulkfeaturemanager.grid.grid_feature_value_list.definition.factory.gridfeaturevaluedefinitionfactory'),
            $request,
            GridFeatureValueDefinitionFactory::GRID_ID,
           'va_bulkfeaturemanager_feature_values',
            ['featureId']
        );
    }

    /**
     * Add new feature value controller
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addNewFeatureValueAction(Request $request){
        // form services
        $unitFeatureValueBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_value_configuration.builder.formbuilder');
        $featureValueFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_value_configuration.form_handler');

        // get form action
        $featureValueForm = $unitFeatureValueBuilder->getForm();
        $featureValueForm->handleRequest($request);

        // handle form request
        $result = $featureValueFormHandler->handle($featureValueForm);

        // if form is valid and submitted, add new data to database
        if($result->isValid() && $result->isSubmitted() && $result->getIdentifiableObjectId() !== null ){
            // get feature name from form
            $featureName = $this->getFeatureName($featureValueForm->getData()['unit_feature_id']);

            $this->addFlash('success',  $this->trans('A new feature value has been successfully created (Added to feature named: %feature_name% id: %feature_id%)', 'Admin.Va_bulkfeaturemanager.Feature', ['%feature_id%' => $featureValueForm->getData()['unit_feature_id'], '%feature_name%' => $featureName]));
            return $this->redirectToRoute('va_bulkfeaturemanager_features_list');
        }
        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/form/feature/feature-form.html.twig', [
            'enableSidebar' => true,
            'feature_form' => $featureValueForm->createView(),
        ]);
    }

    /**
     * Get feature name by providing featureId
     * @param $featureId
     * @return string
     */
    public function getFeatureName($featureId){
        $featureRepository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_configuration_repository');
        //        Find feature by id
        $feature = $featureRepository->findOneById($featureId);
        // return feature name, if not exists return alert 'Feature name not found'
        return $feature->getUnitFeatureName() !== null ? $feature->getUnitFeatureName() : $this->trans('Feature name not found', 'Admin.Va_bulkfeaturemanager.FeatureValueConfiguration');
    }

    /**
     * Edit controller for featureValue form
     * @param Request $request
     * @param int $featureValueId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editFeatureValueAction(Request $request, int $featureValueId)
    {
        // form services
        $unitFeatureValueBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_value_configuration.builder.formbuilder');
        $unitFeatureValueFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.unit_feature_value_configuration.form_handler');

        // get data from form
        $unitFeatureValueForm = $unitFeatureValueBuilder->getFormFor($featureValueId);
        $unitFeatureValueForm->handleRequest($request);
            // handle form
        $result = $unitFeatureValueFormHandler->handleFor($featureValueId, $unitFeatureValueForm);
        // if form is submitted and valid return flash message
        if($result->isSubmitted() && $result->isValid()){
            $this->addFlash('success', $this->trans('Successful update', 'Admin.Va_bulkfeaturemanager.Success'));
            return $this->redirectToRoute('va_bulkfeaturemanager_feature_values', ['featureId' => $unitFeatureValueForm->getData()['unit_feature_id']]);
        }

        return $this->render('@Modules/va_bulkfeaturemanager/views/templates/admin/form/feature/feature-form.html.twig', [
            'feature_form' => $unitFeatureValueForm->createView(),
        ]);
    }

    /**
     * Delete controller for featureValue
     * @param int $featureValueId - to delete specific featureValue
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteFeatureValue(int $featureValueId)
    {
        $repository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_value_configuration_repository');
        try{
            // check if feature exists
            $featureValue = $repository->findOneById($featureValueId);
        } catch (EntityNotFoundException $e){
            $featureValue = null;
        }

        if(null !== $featureValue){
            // If featureValue is not null, delete record
            $entityManager = $this->get('doctrine.orm.entity_manager');
            $entityManager->remove($featureValue);
            $entityManager->flush();
            // display flash message
            $this->addFlash('success', $this->trans('Feature value has been successfully deleted', 'Admin.Va_bulkfeaturemanager.Feature'));
        }else{
            $this->addFlash('error', $this->trans('This feature value does not exist', 'Admin.Va_bulkfeaturemanager.Feature'));
        }
        return $this->redirectToRoute('va_bulkfeaturemanager_feature_values', ['featureId' => $feature->getUnitFeature()->getId()]);
    }

    /**
     * Delete controller for bulk delete featureValue action
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteBulkFeatureValue(Request $request){
        // get featureValue id's from bulk
        $featureValueIds = $request->request->get('gridFeatureValueList_bulk');
        $repository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_value_configuration_repository');

        try{
            // check if provided id exists in database
            $featuresValue = $repository->findById($featureValueIds);
        } catch (EntityNotFoundException $e){
            // if not display error flash message and redirect to route features_list
            $featuresValue = null;
            $this->addFlash('error', $this->trans('This feature value does not exist', 'Admin.Va_bulkfeaturemanager.Feature'));
            return $this->redirectToRoute('va_bulkfeaturemanager_features_list');
        }
        // if values exists
        if(!empty($featuresValue)){

            $em = $this->get('doctrine.orm.entity_manager');
            foreach($featuresValue as $featureValueId){
                //delete each item
                $em->remove($featureValueId);
            }
            $em->flush();
            $this->addFlash('success', $this->trans('Successful delete', 'Admin.Va_bulkfeaturemanager.Feature'));
        }
        return $this->redirectToRoute('va_bulkfeaturemanager_feature_values', ['featureId' => $featuresValue[0]->getUnitFeature()->getId()]);
    }

    public function exportFileFeatureProducts(){
        $service = $this->get('prestashop.module.va_bulkfeaturemanager.service.export_unit_features');
        $featureProductRepository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_product_repository');

        $this->redirectToRoute('va_bulkfeaturemanager');
         return $service->exportFeatureProduct($featureProductRepository);
    }

    public function exportFileFeature(){
        $service = $this->get('prestashop.module.va_bulkfeaturemanager.service.export_unit_features');
        $featureRepository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_configuration_repository');
        $this->redirectToRoute('va_bulkfeaturemanager');
        return $service->exportFeatures($featureRepository);
    }

    public function exportFileFeatureValue(){
        $service = $this->get('prestashop.module.va_bulkfeaturemanager.service.export_unit_features');
        $featureValueRepository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_value_configuration_repository');
        $this->redirectToRoute('va_bulkfeaturemanager');
        return $service->exportFeatureValues($featureValueRepository);
    }
}
