<?php

declare(strict_types=1);



$GLOBALS['TL_DCA']['tl_iso_document']['palettes']['cadeaubon'] = '{type_legend},name,type;{config_legend},documentTitle,fileTitle;{template_legend},documentTpl';

\Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('pdftemplate_legend', 'template_legend')
    ->addField('usePdfTemplate', 'pdftemplate_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('appendPdfTemplate', 'pdftemplate_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addLegend('font_legend', 'pdftemplate_legend')
    ->addField('useCustomFonts', 'font_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addLegend('pdfconfig_legend', 'pdftemplate_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_AFTER, true)
    ->addField('pdfFormat', 'pdfconfig_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('pdfOrientation', 'pdfconfig_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('pdfMargin', 'pdfconfig_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('pdfDefaultFont', 'pdfconfig_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('pdfDefaultFontSize', 'pdfconfig_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('pdfCreator', 'pdfconfig_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->addField('pdfAuthor', 'pdfconfig_legend', \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('cadeaubon', 'tl_iso_document')
;
