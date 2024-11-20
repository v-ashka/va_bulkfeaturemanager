<?php

declare(strict_types=1);
namespace Va_bulkfeaturemanager\Install;

use Module;
use PrestaShopBundle\Install\SqlLoader;

class Installer {
        public function install(\Module $module): bool{
            if(!$this->registerHooks($module)){
                return false;
            }

            if(!$this->executeSqlFromFile($module->getLocalPath() . 'src/Install/Database/install.sql')){
                return false;
            }

            return true;
        }

        public function uninstall(\Module $module): bool {

            return $this->executeSqlFromFile($module->getLocalPath() . 'src/Install/Database/uninstall.sql');
        }

        private function executeSqlFromFile(string $filePath): bool{
            if(!file_exists($filePath)){
                return true;
            }
            $allowedCollations = ['utf8mb4_general_ci', 'utf8mb4_unicode_ci'];
            $databaseCollation = \Db::getInstance()->getValue('SELECT @@collation_database');
            $sqlLoader = new SqlLoader();
            $sqlLoader->setMetaData([
                'PREFIX_' => 'ps_',
                'ENGINE_TYPE' => _MYSQL_ENGINE_,
                'COLLATION' => (empty($databaseCollation) || !in_array($databaseCollation, $allowedCollations)) ? '' : 'COLLATE ' . $databaseCollation,
            ]);

            return $sqlLoader->parseFile($filePath);
        }

        private function registerHooks(\Module $module): bool{
            $hooks = [
                'actionFrontControllerSetMedia',
//                'displayFrontFeatureInfo',
//                'displayProductAdditionalInfo',
                'actionProductFormBuilderModifier',
                'displayAdminProductsExtra',
//                'displayAdminProductsMainStepLeftColumnMiddle',
                'actionAdminProductsControllerSaveAfter',
                'actionObjectProductDeleteAfter',
                'actionGetProductPropertiesAfter'
            ];

            return (bool) $module->registerHook($hooks);
        }
}
