<?php

if (!isset($GLOBALS['TL_DCA']['tl_iso_product_collection_item']['list']['sorting']['child_record_callback'])) {
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['list']['sorting']['mode'] = 4;
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['list']['sorting']['flag'] = 11;
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['list']['sorting']['panelLayout'] = 'search';
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['list']['sorting']['disableGrouping'] = true;
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['list']['sorting']['fields'] = ['name'];
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['list']['sorting']['headerFields'] = ['document_number'];
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['list']['sorting']['child_record_callback'] = function($arrRow) {
    $collection = \Isotope\Model\ProductCollection::findByPk($arrRow['pid']);
    return $collection->document_number . ' - ' . $arrRow['name'];
  };
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['fields']['pid']['search'] = true;
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['fields']['pid']['inputType'] = 'text';
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['fields']['name']['search'] = true;
  $GLOBALS['TL_DCA']['tl_iso_product_collection_item']['fields']['name']['inputType'] = 'text';
}