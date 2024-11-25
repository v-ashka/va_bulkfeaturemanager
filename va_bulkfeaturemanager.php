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
            'name' => 'Feature Manager',
            'parent_class_name' => 'CONFIGURE',
            'icon' => "category",
        ],
    ];

    public function __construct()
    {
        $this->name = 'va_bulkfeaturemanager';
        $this->tab = 'administration';
        $this->version = '2.1.0';
        $this->author = 'Marcin Wijaszka';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Unit price manager');
        $this->description = $this->l('This module adds the functionality to display the unit price of a product below the main price. The module allows for bulk assignment of units to products with a unit previously configured by the user.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install(): bool
    {
        if(!parent::install()){
            return false;
        }
        $installer = new Installer();
        return $installer->install($this);


    }

    public function uninstall(): bool
    {
        if(!parent::uninstall()){
            return false;
        }
        $installer = new Installer();
        return $installer->uninstall($this);
    }

    public function hookDisplayProductAdditionalInfo($params){
        $bulkFeatureManagerService = $this->get('prestashop.module.va_bulkfeaturemanager.service.bulkfeaturemanager_service');
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

        if($productFeatures){
            $productPrice = Product::getPriceStatic($productId);
            $calculatedPrice = $this->calculateProductBaseValue(
                (float) $productFeatures[0]['unit_feature_base_value'],
                (float) $productFeatures[0]['value'],
                (float) $productPrice);
            $this->smarty->assign([
                'feature_shortcut' => $productFeatures[0]['unit_feature_shortcut'],
                'feature_base_value' => $productFeatures[0]['unit_feature_base_value'],
                'currency' => $this->context->currency->symbol,
                'calculated_price' => $calculatedPrice
            ]);
            return $this->fetch('module:va_bulkfeaturemanager/views/templates/front/featureinfo.tpl');
        }

    }

    public function calculateProductBaseValue( float $baseValue, float $featureValue, float $price){
        $result = ($price * $baseValue) / $featureValue;
        return number_format($result, 2, '.', '');
    }

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

    public function hookDisplayAdminProductsExtra($params){
        $productId = $params['id_product'];
        $twig = $this->get('twig');
        $unitFeatureValueBuilder = $this->get('prestashop.module.va_bulkfeaturemanager.form.admin_products_extra_configuration.form_builder');
        $unitFeatureValueForm = $unitFeatureValueBuilder->getFormFor($productId);
        $unitFeatureValueForm->handleRequest($params['request']);
        $template = '@Modules/va_bulkfeaturemanager/views/templates/admin/admin_extra_form.html.twig';

        $unitProductRepository = $this->get('prestashop.module.va_bulkfeaturemanager.repository.unit_feature_product_repository');
        $productExistCheck = $unitProductRepository->countById(['idProductAttribute' => $productId]);

        return $twig->render($template, [
            'additional_info' => $productExistCheck,
            'admin_extra_form' => $unitFeatureValueForm->createView(),
        ]);
    }


    public function hookActionAdminProductsControllerSaveAfter($params){
        $unitFeatureValueFormHandler = $this->get('prestashop.module.va_bulkfeaturemanager.form.admin_products_extra_configuration.unit_feature_products_extra.data_handler');
        $data = Tools::getValue('unit_feature_products_extra_form');
        $id = Tools::getValue('id_product');
        $unitFeatureValueFormHandler->update((int) $id, $data);
    }


}
