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

//    SELECT p.* FROM `ps_feature_product` fp INNER JOIN ps_product p WHERE p.id_product = fp.id_product;

    //INSERT INTO `ps_feature_product` (id_feature, id_product, id_feature_value)
    //VALUES
    //(3, 1, 11),
    //(3, 2, 12),
    //(3, 3, 13),
    //(3, 4, 14),
    //(3, 5, 15),
    //(3, 6, 12);


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
        Configuration::updateValue('VA_BULKFEATUREMANAGER_LIVE_MODE', false);

        return parent::install();
    }

    public function uninstall()
    {
        Configuration::deleteByName('VA_BULKFEATUREMANAGER_LIVE_MODE');

        return parent::uninstall();
    }
}
