<?php
/**
* 2007-2015 PrestaShop
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
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class TaxRulesByCustomerGroup extends Module
{
    public function __construct()
    {
        $this->name = 'taxrulesbycustomergroup';
        $this->tab = 'billing_invoicing';
        $this->version = '1.0.0';
        $this->author = 'Cooperativa Banquito';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Tax Rules By Customer Group');
        $this->description = $this->l('Enables you to you to assign tax rules depending on customer group (among other filters).');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install() || !$this->alterTable('add') || !$this->overrideFiles()) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->alterTable('remove') || !$this->removeOverrideFiles()) {
            return false;
        }

        return true;
    }

    private function alterTable($method)
    {
        switch ($method) {
            case 'add':
                $sql = 'ALTER TABLE '._DB_PREFIX_.'tax_rule ADD `id_group` int(10) DEFAULT NULL;';
                break;
            case 'remove':
                $sql = 'ALTER TABLE '._DB_PREFIX_.'tax_rule DROP COLUMN `id_group`;';
                break;
        }

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        return true;
    }

    protected static function getOverrideInfo()
    {
        return array(
            'TaxRule.php' => array(
                'source' => 'override/classes/tax/TaxRule.php',
                'dest' => 'override/classes/tax/TaxRule.php',
                ),
            'TaxRulesTaxManager.php' => array(
                'source' => 'override/classes/tax/TaxRulesTaxManager.php',
                'dest' => 'override/classes/tax/TaxRulesTaxManager.php',
                ),
            'AdminTaxRulesGroupController.php' => array(
                'source' => 'override/controllers/admin/AdminTaxRulesGroupController.php',
                'dest' => 'override/controllers/admin/AdminTaxRulesGroupController.php',
                ),
            'form.tpl' => array(
                'source' => 'views/templates/admin/tax_rules/helpers/form/form.tpl',
                'dest' => 'override/controllers/admin/templates/tax_rules/helpers/form/form.tpl',
                ),
            );
    }

    protected function overrideFiles()
    {
        if ($this->removeOverrideFiles()) {
            /* Check if the override directories exists */
            if (!is_dir(_PS_ROOT_DIR_.'/override/classes/')) {
                mkdir(_PS_ROOT_DIR_.'/override/classes/', 0777, true);
            }
            if (!is_dir(_PS_ROOT_DIR_.'/override/controllers/')) {
                mkdir(_PS_ROOT_DIR_.'/override/controllers/', 0777, true);
            }
            if (!is_dir(_PS_ROOT_DIR_.'/override/controllers/admin/templates/tax_rules/helpers/form/')) {
                mkdir(_PS_ROOT_DIR_.'/override/controllers/admin/templates/tax_rules/helpers/form/', 0777, true);
            }

            foreach (self::getOverrideInfo() as $key => $params) {
                if (file_exists(_PS_ROOT_DIR_.'/'.$params['dest'])) {
                    $this->_errors[] = $this->l('This override file already exists, please merge it manually: ').$key;
                } elseif (!copy(_PS_MODULE_DIR_.'taxrulesbycustomergroup/'.$params['source'], _PS_ROOT_DIR_.'/'.$params['dest'])) {
                    $this->_erroors[] = $this->l('Error while copying the override file: ').$key;
                }
            }
        }

        return !isset($this->_errors) || !$this->_errors || !count($this->_errors);
    }

    protected function removeOverrideFiles()
    {
        foreach (self::getOverrideInfo() as $key => $params) {
            if (!file_exists(_PS_ROOT_DIR_.'/'.$params['dest'])) {
                continue;
            }

            $removed = false;

            if (unlink(_PS_ROOT_DIR_.'/'.$params['dest'])) {
                $removed = true;
            }

            if (!$removed) {
                $this->_errors[] = $this->l('Error while removing override: ').$key;
            }
        }

        return !isset($this->_errors) || !$this->_errors || !count($this->_errors);
    }
}
