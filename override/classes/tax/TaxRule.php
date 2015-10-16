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

class TaxRule extends TaxRuleCore
{
    public $id_group;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'tax_rule',
        'primary' => 'id_tax_rule',
        'fields' => array(
            'id_tax_rules_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_state' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'zipcode_from' => array('type' => self::TYPE_STRING, 'validate' => 'isPostCode'),
            'zipcode_to' => array('type' => self::TYPE_STRING, 'validate' => 'isPostCode'),
            'id_tax' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'behavior' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'description' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_group' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    public static function getTaxRulesByGroupId($id_lang, $id_group)
    {
        return Db::getInstance()->executeS('
		SELECT g.`id_tax_rule`,
				 c.`name` AS country_name,
				 s.`name` AS state_name,
				 t.`rate`,
				 g.`zipcode_from`, g.`zipcode_to`,
				 g.`description`,
				 g.`behavior`,
				 g.`id_group`,
				 g.`id_country`,
				 g.`id_state`
		FROM `'._DB_PREFIX_.'tax_rule` g
		LEFT JOIN `'._DB_PREFIX_.'country_lang` c ON (g.`id_country` = c.`id_country` AND `id_lang` = '.(int) $id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'state` s ON (g.`id_state` = s.`id_state`)
		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (g.`id_tax` = t.`id_tax`)
		WHERE `id_tax_rules_group` = '.(int) $id_group.'
		ORDER BY `country_name` ASC, `state_name` ASC, `zipcode_from` ASC, `zipcode_to` ASC'
        );
    }
}
