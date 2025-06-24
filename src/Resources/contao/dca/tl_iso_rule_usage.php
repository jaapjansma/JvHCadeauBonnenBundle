<?php

if (!isset($GLOBALS['TL_DCA']['tl_iso_rule_usage']['config']['dataContainer'])) {
  $GLOBALS['TL_DCA']['tl_iso_rule_usage']['config']['dataContainer'] = 'Table';
  $GLOBALS['TL_DCA']['tl_iso_rule_usage']['config']['ptable'] = 'tl_iso_rule';
  $GLOBALS['TL_DCA']['tl_iso_rule_usage']['config']['notEditable'] = true;
  $GLOBALS['TL_DCA']['tl_iso_rule_usage']['config']['notCreatable'] = true;
  $GLOBALS['TL_DCA']['tl_iso_rule_usage']['list']['sorting']['mode'] = 4;
  $GLOBALS['TL_DCA']['tl_iso_rule_usage']['list']['sorting']['flag'] = 11;
  $GLOBALS['TL_DCA']['tl_iso_rule_usage']['list']['sorting']['disableGrouping'] = true;
  $GLOBALS['TL_DCA']['tl_iso_rule_usage']['list']['sorting']['headerFields'] = ['name', 'label'];
  $GLOBALS['TL_DCA']['tl_iso_rule_usage']['list']['sorting']['child_record_callback'] = function($arrRow) {
    $collection = \Isotope\Model\ProductCollection::findByPk($arrRow['order_id']);
    return $collection->document_number;
  };
}