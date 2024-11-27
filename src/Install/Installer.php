<?php

declare(strict_types=1);
namespace Va_bulkfeaturemanager\Install;

use Module;
use PrestaShopBundle\Install\SqlLoader;

class Installer {
        public function install(\Module $module): bool{
//            Register listed hooks
            if(!$this->registerHooks($module)){
                return false;
            }
//            Create new tables, and insert new data from selected install sql path
            if(!$this->executeSqlFromFile($module->getLocalPath() . 'src/Install/Database/install.sql')){
                return false;
            }
            return true;
        }

        public function uninstall(\Module $module): bool {
//            Delete tables if exists
            return $this->executeSqlFromFile($module->getLocalPath() . 'src/Install/Database/uninstall.sql');
        }
    /**
     * Executes SQL instructions from a file, taking into account database settings.
     *
     * @param string $filePath Path to the SQL file
     * @return bool Whether the operation was successful
     */
        private function executeSqlFromFile(string $filePath): bool{
//            Check if file exists
            if(!file_exists($filePath)){
                return true;
            }
            $allowedCollations = ['utf8mb4_general_ci', 'utf8mb4_unicode_ci'];
            $databaseCollation = \Db::getInstance()->getValue('SELECT @@collation_database');
            $sqlLoader = new SqlLoader();
//            Set metadata for MySQL db
            $sqlLoader->setMetaData([
                'PREFIX_' => 'ps_',
                'ENGINE_TYPE' => _MYSQL_ENGINE_,
                'COLLATION' => (empty($databaseCollation) || !in_array($databaseCollation, $allowedCollations)) ? '' : 'COLLATE ' . $databaseCollation,
            ]);

            return $sqlLoader->parseFile($filePath);
        }

    /**
     * Registers necessary hooks for the module.
     *
     * @param \Module $module Module instance
     * @return bool Whether hook registration was successful
     */
        private function registerHooks(\Module $module): bool{
            $hooks = [
                // Set media for front controller
                'actionFrontControllerSetMedia',
                // Display additional product information
                'displayProductAdditionalInfo',
                // Display additional fields in product admin panel
                'displayAdminProductsExtra',
                // Action performed after saving a product in admin panel (ver. < 8.1 Prestashop)
                'actionAdminProductsControllerSaveAfter',
                // Action performed after saving a product in admin panel (ver. > 8.1 Prestashop)
                'actionProductFormBuilderModifier'

            ];
            // Register all defined hooks
            return (bool) $module->registerHook($hooks);
        }
}
