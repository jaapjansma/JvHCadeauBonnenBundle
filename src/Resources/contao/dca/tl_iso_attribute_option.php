<?php

/**
 * Table tl_iso_product
 */
$GLOBALS['TL_DCA']['tl_iso_attribute_option']['fields']['cadeaubon_per_email'] = array
(
  'label'                 => &$GLOBALS['TL_LANG']['tl_iso_attribute_option']['cadeaubon_per_email'],
  'exclude'               => true,
  'inputType'             => 'checkbox',
  'eval'                  => array('tl_class'=>'w50 m12', 'doNotCopy'=>true),
  'sql'                   => "char(1) NOT NULL default ''",
);

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
  ->addField('cadeaubon_per_email', 'isDefault', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER)
  ->applyToPalette('option', 'tl_iso_attribute_option');