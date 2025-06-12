<?php

/**
 * Table tl_iso_product
 */
$GLOBALS['TL_DCA']['tl_iso_product']['fields']['cadeaubon_bedrag'] = array
(
    'label'                 => &$GLOBALS['TL_LANG']['tl_iso_product']['cadeaubon_bedrag'],
    'exclude'               => true,
    'search'                => true,
    'sorting'               => true,
    'inputType'             => 'text',
    'eval'                  => array('mandatory'=>true, 'maxlength'=>13, 'rgxp'=>'price'),
    'attributes'            => array('legend'=>'pricing_legend', 'fixed'=>true, 'customer_defined'=>false),
    'sql'                   => "decimal(12,2) NOT NULL default '0.00'",
);
