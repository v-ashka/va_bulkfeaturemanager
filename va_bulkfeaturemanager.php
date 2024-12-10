<?php
/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
declare(strict_types=1);

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';

use Va_bulkfeaturemanager\Install\Installer;

class Va_bulkfeaturemanager extends Module
{
    protected $config_form = false;


    public $tabs = [
        [
            'class_name' => 'BulkFeatureManagerController',
            'route_name' => 'va_bulkfeaturemanager',
            'visible' => true,
            'name' => 'Unit Price Manager',
            'parent_class_name' => 'CONFIGURE',
            'icon' => "category",
        ],
    ];

    public function __construct()
    {
        $this->name = 'va_bulkfeaturemanager';
        $this->tab = 'administration';
        $this->version = '2.1.1';
        $this->author = 'Marcin Wijaszka';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Unit Price Manager');
        $this->description = $this->l('This module adds the functionality to display the unit price of a product below the main price. The module allows for bulk assignment of units to products with a unit previously configured by the user.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Install require elements
     */
    public function install(): bool
    {
        if(!parent::install()){
            return false;
        }
        $installer = new Installer();
        return $installer->install($this);


    }
    /**
     * Uninstall require elements like database data
     */
    public function uninstall(): bool
    {
        if(!parent::uninstall()){
            return false;
        }
        $installer = new Installer();
        return $installer->uninstall($this);
    }

    /**
     * Display calculated unit price depends on product id
     * @param $params
     * @return string|void
     */
    public function hookDisplayProductAdditionalInfo($params){
        $bulkFeatureManagerService = $this->get('prestashop.module.va_bulkfeaturemanager.service.bulkfeaturemanager_service');
//        Get product and id_product_attribute id
        $productId = Tools::getValue('id_product');
        $productAttribute = Tools::getValue('id_product_attribute');
        $productFeatures = [];

        if($productAttribute){
//            TODO product attribute get feature
                $productAttribute = [];
//            $productFeatures = $bulkFeatureManagerService->getFeatureByProductAttributeId((int) $params['product']->id_product_attribute);
        }else{
            $productFeatures = $bulkFeatureManagerService->getFeatureByProductId((int) $productId);
        }

//        If product feature has been mapped, calculate unit price
        if($productFeatures){
//            get static price from product
            $productPrice = Product::getPriceStatic($productId);
            $calculatedPrice = $this->calculateProductBaseValue(
//                unit feature base value
                (float) $productFeatures[0]['unit_feature_base_value'],
//                product value
                (float) $productFeatures[0]['value'],
//                product price
                (float) $productPrice);
            $this->smarty->assign([
                'feature_shortcut' => $productFeatures[0]['unit_feature_shortcut'],
                'feature_base_value' => $productFeatures[0]['unit_feature_base_value'],
                'currency' => $this->context->currency->symbol,
                'calculated_price' => $calculatedPrice
            ]);
//            fetch values to file
            return $this->fetch('module:va_bulkfeaturemanager/views/templates/front/featureinfo.tpl');
        }

    }

    /**
     * Calculate product unit price
     * @param float $baseValue - this value is set in module configuration page
     * @param float $featureValue - this value is set in module configuration page
     * @param float $price - this value is fetching from product using getPriceStatic() method
     * @return string
     */
    public function calculateProductBaseValue( float $baseValue, float $featureValue, float $price){
        $result = ($price * $baseValue) / $featureValue;
//        Always return value with 2 decimals after separator (ex. 2.40 )
        return number_format($result, 2, '.', '');
    }

    /**
     * Attach module stylesheet to front controller
     * @return void
     */
    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'va_bulkfeaturemanager',
            'modules/' . $this->name . '/views/css/front.css',
            [
                'media' => 'all',
                'priority' => 1000,
            ]
        );
    }

    /**
     * Display extra tab in product configuration page.
     * This hook displays extra form to modify feature inside product configuration page
     * @param $params
     * @return mixed
     * @throws Exception
     */
    public function hookDisplayAdminProductsExtra($params){
//        Get product id value
        $productId = $params['id_product'];
//        Register twig for render template form
        $twig = $this->get('twig');

//        Unit feature value builder service to manage form
        $unitFeatureValueBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.form.admin_products_extra_configuration.form_builder');
        $unitFeatureValueForm = $unitFeatureValueBuilder->getFormFor($productId);
        $unitFeatureValueForm->handleRequest($params['request']);

//        Check if product have attached any features
        $unitProductRepository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_product_repository');
//        If productExistCheck -> 0 set form title to "Add new feature to product" else display "Modify feature in product"
        $productExistCheck = $unitProductRepository->countById(['idProductAttribute' => $productId]);

        $template = '@Modules/va_bulkfeaturemanager/views/templates/admin/admin_extra_form.html.twig';
        return $twig->render($template, [
            'additional_info' => $productExistCheck,
            'admin_extra_form' => $unitFeatureValueForm->createView(),
        ]);
    }

    /**
     * Save set values in form and send to feature product service
     * Backward compatibility with PrestaShop ver. < 8.1
     * @param $params
     * @return void
     * @throws Exception
     */
    public function hookActionAdminProductsControllerSaveAfter($params){
//        Get feature product form handler service class
        $unitFeatureProductFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.admin_products_extra_configuration.unit_feature_products_extra.data_handler');
//        Get array values with keys: feature_id and feature_value_id from 'unit_feature_products_extra_form' form
//        (ex. ["feature_id" => "1", "feature_val_id" => "2"] )
        $data = Tools::getValue('unit_feature_products_extra_form');
//        Get product id
        $id = Tools::getValue('id_product');
//        Send saved data to update method
        $unitFeatureProductFormHandler->update((int) $id, $data);
    }

    /**
     * Save set values in form and send to feature product service (For PrestaShop ver. >= 8.1 )
     * @param $params
     * @return void
     * @throws Exception
     */
    public function hookActionProductFormBuilderModifier($params){
        // Get feature product form handler service class
        $unitFeatureProductFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.admin_products_extra_configuration.unit_feature_products_extra.data_handler');
        // Get array values with keys: feature_id and feature_value_id from 'unit_feature_products_extra_form' form
        // (ex. ["feature_id" => "1", "feature_val_id" => "2"] )
        $data = Tools::getValue('unit_feature_products_extra_form');
        // Get product id
        $id = Tools::getValue('id_product');
        // Send saved data to update method
        if($data !== false)
            $unitFeatureProductFormHandler->update((int) $id, $data);
    }

}
