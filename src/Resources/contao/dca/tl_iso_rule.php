<?php

use \Haste\Dca\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_iso_rule']['list']['operations']['send_email'] = [
  'label' => &$GLOBALS['TL_LANG']['tl_iso_rule']['send_email'],
  'icon' => 'resend.svg',
  'href' => 'key=send_email',
  'button_callback' => function ($row, $href, $label, $title, $icon, $attributes) {
    if (!empty($row['code']) && !empty($row['enabled']) && !empty($row['jvh_cadeaubon']) && !empty($row['email'])) {
      return '<a href="' . \Contao\Backend::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . Contao\StringUtil::specialchars($title) . '"' . $attributes . '>' . Contao\Image::getHtml($icon, $label) . '</a> ';
    }
    return '';
  }
];

$GLOBALS['TL_DCA']['tl_iso_rule']['list']['operations']['usage'] = [
  'label' => &$GLOBALS['TL_LANG']['tl_iso_rule']['usage'],
  'icon' => 'db.svg',
  'href' => 'table=tl_iso_rule_usage',
  'button_callback' => function ($row, $href, $label, $title, $icon, $attributes) {
    $db = Contao\Database::getInstance();
    $objResult = $db->prepare("SELECT COUNT(*) as `total` FROM tl_iso_rule_usage WHERE pid=?")->execute($row['id']);
    if ($objResult->first() && $objResult->total) {
      return '<a href="' . \Contao\Backend::addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . Contao\StringUtil::specialchars($title) . '"' . $attributes . '>' . Contao\Image::getHtml($icon, $label) . '</a> ';
    }
    return '';
  }
];

$GLOBALS['TL_DCA']['tl_iso_rule']['fields']['jvh_cadeaubon'] = [
  'label' => &$GLOBALS['TL_LANG']['tl_iso_rule']['jvh_cadeaubon'],
  'exclude' => true,
  'filter' => true,
  'inputType' => 'checkbox',
  'eval' => array('submitOnChange' => true),
  'sql' => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_iso_rule']['fields']['original_discount'] = [
  'label' => &$GLOBALS['TL_LANG']['tl_iso_rule']['original_discount'],
  'exclude' => true,
  'search' => true,
  'inputType' => 'text',
  'eval' => array('mandatory' => false, 'readonly' => true, 'maxlength' => 16, 'rgxp' => 'discount', 'tl_class' => 'clr w50'),
  'sql' => "varchar(16) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_iso_rule']['fields']['product_collection_item_id'] = [
  'label' => &$GLOBALS['TL_LANG']['tl_iso_rule']['product_collection_item_id'],
  'inputType' => 'picker',
  'sql' => "int(10) unsigned NOT NULL default '0'",
  'relation'                => array('type'=>'hasOne', 'load'=>'lazy', 'table'=>'tl_iso_product_collection_item'),
  'eval' => array('readonly' => true),
];

$GLOBALS['TL_DCA']['tl_iso_rule']['fields']['email'] = [
  'label' => &$GLOBALS['TL_LANG']['tl_iso_rule']['email'],
  'exclude' => true,
  'search' => true,
  'inputType' => 'text',
  'eval' => array('mandatory' => false, 'maxlength' => 255, 'rgxp' => 'email', 'unique' => true, 'decodeEntities' => true, 'tl_class' => 'clr'),
  'sql' => "varchar(255) NOT NULL default ''"
];

PaletteManipulator::create()
  ->addField('original_discount', 'discount', PaletteManipulator::POSITION_BEFORE)
  ->applyToPalette('product', 'tl_iso_rule');
PaletteManipulator::create()
  ->addField('original_discount', 'discount', PaletteManipulator::POSITION_BEFORE)
  ->applyToPalette('cart', 'tl_iso_rule');
PaletteManipulator::create()
  ->addField('original_discount', 'discount', PaletteManipulator::POSITION_BEFORE)
  ->applyToPalette('cartsubtotal', 'tl_iso_rule');
PaletteManipulator::create()
  ->addField('jvh_cadeaubon', 'code', PaletteManipulator::POSITION_AFTER)
  ->applyToSubpalette('enableCode', 'tl_iso_rule');
PaletteManipulator::create()
  ->addLegend('order_legend', 'basic_legend', PaletteManipulator::POSITION_APPEND)
  ->addField('email', 'order', PaletteManipulator::POSITION_AFTER)
  ->addField('product_collection_item_id', 'email', PaletteManipulator::POSITION_AFTER)
  ->applyToPalette('cart', 'tl_iso_rule');
PaletteManipulator::create()
  ->addLegend('order_legend', 'basic_legend', PaletteManipulator::POSITION_APPEND)
  ->addField('email', 'order', PaletteManipulator::POSITION_AFTER)
  ->addField('product_collection_item_id', 'email', PaletteManipulator::POSITION_AFTER)
  ->applyToPalette('cartsubtotal', 'tl_iso_rule');
PaletteManipulator::create()
  ->addLegend('order_legend', 'basic_legend', PaletteManipulator::POSITION_APPEND)
  ->addField('email', 'order', PaletteManipulator::POSITION_AFTER)
  ->addField('product_collection_item_id', 'email', PaletteManipulator::POSITION_AFTER)
  ->applyToPalette('cart_group', 'tl_iso_rule');