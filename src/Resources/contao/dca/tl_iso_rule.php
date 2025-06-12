<?php

use \Haste\Dca\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_iso_rule']['fields']['jvh_cadeaubon'] = [
    'label'                         => &$GLOBALS['TL_LANG']['tl_iso_rule']['jvh_cadeaubon'],
    'exclude'                       => true,
    'filter'                        => true,
    'inputType'                     => 'checkbox',
    'eval'                          => array('submitOnChange'=>true),
    'sql'                           => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_iso_rule']['fields']['original_discount'] = [
  'label'                         => &$GLOBALS['TL_LANG']['tl_iso_rule']['original_discount'],
  'exclude'                       => true,
  'search'                        => true,
  'inputType'                     => 'text',
  'eval'                          => array('mandatory'=>false, 'readonly' => true, 'maxlength'=>16, 'rgxp'=>'discount', 'tl_class'=>'clr w50'),
  'sql'                           => "varchar(16) NOT NULL default ''",
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