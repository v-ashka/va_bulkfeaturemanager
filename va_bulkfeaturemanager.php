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

if (!defined('_PS_VERSION_')) {
    exit;
}

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
        $this->version = '1.0.0';
        $this->author = 'Marcin Wijaszka';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Bulk Product Feature Manager');
        $this->description = $this->l('This module can add new feature to products');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('displayFrontFeatureInfo');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            $this->unregisterHook('actionFrontControllerSetMedia') &&
            $this->unregisterHook('displayFrontFeatureInfo');
    }

    public function hookDisplayFrontFeatureInfo($params){
        $bulkFeatureManagerService = $this->get('prestashop.module.va_bulkfeaturemanager.service.bulkfeaturemanager_service');
        $productId = Tools::getValue('id_product');
        $productFeatures = $bulkFeatureManagerService->getFeatureByProductId($productId);

            $specialFeature = $this->extractSpecialFeature(3, $productFeatures, true);
            $productPrice = Product::getPriceStatic(
                $productId,
                true, // z podatkiem
                null, // id kombinacji produktu (jeśli istnieje)
                Context::getContext()->getComputingPrecision(), // precyzja
                null,
                false,
                true, // z uwzględnieniem rabatów
                1 // ilość
            );
            $baseLiterCapacity = 100;
            dd($specialFeature);
            $pricePerLiter = round(( $productPrice * $baseLiterCapacity ) / ((int) $specialFeature[0]['value']), '2');

            $this->smarty->assign([
                'features' => $this->extractSpecialFeature(3, $productFeatures, true),
                'pricePerBaseUnit' => $pricePerLiter,
                'baseUnitCapacity' => $baseLiterCapacity,
                'currency' => $this->context->currency->symbol,
                'baseUnitSymbol' => $specialFeature[0]['id_feature'] === 3 ? 'L' : 'kg'
            ]);
            return $this->fetch('module:va_bulkfeaturemanager/views/templates/front/featureinfo.tpl');


    }

    private function extractSpecialFeature(int $featureId, array $productFeatures, bool $displayExtractedArray = false): array{
        return current(array_filter($productFeatures, function($item) use ($displayExtractedArray, $featureId) {
            return $displayExtractedArray ?
                ($item['id_feature'] !== $featureId ) : ($item['id_feature'] === $featureId);
        }));
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
}
