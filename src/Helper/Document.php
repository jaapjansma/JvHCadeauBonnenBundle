<?php
/**
 * Copyright (C) 2025  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace JvH\CadeauBonnenBundle\Helper;

use Contao\Controller;
use Contao\Environment;
use Contao\File;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use Haste\Util\StringUtil as HasteStringUtil;
use Isotope\Template;

class Document {

  /**
   * {@inheritdoc}
   */
  public static function outputToFile($objDocument, array $arrTokens, $strDirectoryPath)
  {
    $pdf        = self::generatePDF($objDocument, $arrTokens);
    $strFile    = self::prepareFileName($objDocument->fileTitle, $arrTokens, $strDirectoryPath) . '.pdf';

    $pdf->Output(
      $strFile,
      'F'
    );

    return $strFile;
  }

  /**
   * Prepare file name
   *
   * @param string $strName   File name
   * @param array  $arrTokens Simple tokens (optional)
   * @param string $strPath   Path (optional)
   *
   * @return string Sanitized file name
   */
  protected static function prepareFileName($strName, $arrTokens = array(), $strPath = '')
  {
    // Replace simple tokens
    $strName = self::sanitizeFileName(
      HasteStringUtil::recursiveReplaceTokensAndTags(
        $strName,
        $arrTokens,
        HasteStringUtil::NO_TAGS | HasteStringUtil::NO_BREAKS | HasteStringUtil::NO_ENTITIES
      )
    );

    if ($strPath) {
      // Make sure the path contains a trailing slash
      $strPath = preg_replace('/([^\/]+)$/', '$1/', $strPath);

      $strName = $strPath . $strName;
    }

    return $strName;
  }

  /**
   * Sanitize file name
   *
   * @param string $strName              File name
   * @param bool   $blnPreserveUppercase Preserve uppercase (true by default)
   *
   * @return string Sanitized file name
   */
  protected static function sanitizeFileName($strName, $blnPreserveUppercase = true)
  {
    return StringUtil::standardize(ampersand($strName, false), $blnPreserveUppercase);
  }

  /**
   * Generate the pdf document
   *
   * @param                         $objDocument
   * @param array                    $arrTokens
   *
   * @return \TCPDF
   */
  protected static function generatePDF($objDocument, array $arrTokens)
  {
    // Get the project directory
    $projectDir = System::getContainer()->getParameter('kernel.project_dir');

    // Include TCPDF config
    if (file_exists($projectDir.'/system/config/tcpdf.php')) {
      require_once $projectDir.'/system/config/tcpdf.php';
    } elseif (file_exists($projectDir.'/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php')) {
      require_once $projectDir.'/vendor/contao/core-bundle/src/Resources/contao/config/tcpdf.php';
    } elseif (file_exists($projectDir.'/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php')) {
      require_once $projectDir.'/vendor/contao/tcpdf-bundle/src/Resources/contao/config/tcpdf.php';
    }

    $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    // Add custom fonts
    if ($objDocument->useCustomFonts) {
      if (null !== ($folder = FilesModel::findByUuid($objDocument->customFontsDirectory))) {
        $fontDirs[] = $projectDir.'/'.$folder->path;

        $config = StringUtil::deserialize($objDocument->customFontsConfig, true);
        if (!empty($config)) {
          foreach ($config as $font) {
            if (!empty($font['fontname']) && $font['enabled']) {
              $fontData[$font['fontname']][$font['variant']] = $font['filename'];
            }
          }
        }
      }
    }

    $margin = StringUtil::deserialize($objDocument->pdfMargin, true);

    // Create new PDF document
    $pdf = new \Mpdf\Mpdf([
      'fontDir' => $fontDirs,
      'fontdata' => $fontData,
      'format' => $objDocument->pdfFormat ?: (\defined('PDF_PAGE_FORMAT') ? PDF_PAGE_FORMAT : 'A4'),
      'orientation' => $objDocument->pdfOrientation ?: (\defined('PDF_PAGE_ORIENTATION') ? PDF_PAGE_ORIENTATION : 'P'),
      'margin_left' => (int) ($margin['left'] ?? (\defined('PDF_MARGIN_LEFT') ? PDF_MARGIN_LEFT : 15)),
      'margin_right' => (int) ($margin['right'] ?? (\defined('PDF_MARGIN_RIGHT') ? PDF_MARGIN_RIGHT : 15)),
      'margin_top' => (int) ($margin['top'] ?? (\defined('PDF_MARGIN_TOP') ? PDF_MARGIN_TOP : 10)),
      'margin_bottom' => (int) ($margin['bottom'] ?? (\defined('PDF_MARGIN_BOTTOM') ? PDF_MARGIN_BOTTOM : 10)),
      'default_font_size' => (int) ($objDocument->pdfDefaultFontSize ?: (\defined('PDF_FONT_SIZE_MAIN') ? PDF_FONT_SIZE_MAIN : 1)),
      'default_font' => $objDocument->pdfDefaultFont ?: (\defined('PDF_FONT_NAME_MAIN') ? PDF_FONT_NAME_MAIN : 'freeserif'),
    ]);

    // Set document information
    $pdf->SetCreator($objDocument->pdfCreator);
    $pdf->SetAuthor($objDocument->pdfAuthor ?: Environment::get('url'));
    $pdf->SetTitle(StringUtil::parseSimpleTokens($objDocument->documentTitle, $arrTokens));

    // Check to use template
    if ($objDocument->usePdfTemplate) {
      // Find file in database
      if (null !== ($file = FilesModel::findById($objDocument->usePdfTemplateSRC))) {
        // Check if file exists
        if (file_exists($projectDir.'/'.$file->path)) {
          $pdf->SetDocTemplate($projectDir.'/'.$file->path, true);
        }
      }
    }

    // Initialize document and add a page
    $pdf->AddPage();

    // Write the HTML content
    $pdf->WriteHTML(self::generateTemplate($objDocument, $arrTokens));

    // Reset template
    $pdf->SetDocTemplate();

    // Check to append PDF
    if ($objDocument->appendPdfTemplate) {
      // Find file in database
      if (null !== ($file = FilesModel::findById($objDocument->appendPdfTemplateSRC))) {
        // Check if file exists
        if (file_exists($projectDir.'/'.$file->path)) {
          $pagecount = $pdf->SetSourceFile($projectDir.'/'.$file->path);

          for ($i = 1; $i <= $pagecount; ++$i) {
            $pdf->AddPage();
            $tpl = $pdf->ImportPage($i);
            $pdf->UseTemplate($tpl);
          }
        }
      }
    }

    return $pdf;
  }

  /**
   * Generate and return document template
   *
   * @param                         $objDocument
   * @param array                    $arrTokens
   *
   * @return string
   */
  protected static function generateTemplate($objDocument, array $arrTokens)
  {
    /** @var Template|\stdClass $objTemplate */
    $objTemplate = new Template($objDocument->documentTpl);
    $objTemplate->setData($objDocument->arrData);

    $objTemplate->title         = StringUtil::parseSimpleTokens($objDocument->documentTitle, $arrTokens);
    $objTemplate->tokens           = $arrTokens;
    $objTemplate->dateFormat    = $objPage->dateFormat ?? $GLOBALS['TL_CONFIG']['dateFormat'];
    $objTemplate->timeFormat    = $objPage->timeFormat ?? $GLOBALS['TL_CONFIG']['timeFormat'];
    $objTemplate->datimFormat   = $objPage->datimFormat ?? $GLOBALS['TL_CONFIG']['datimFormat'];

    // Generate template and fix PDF issues, see Contao's ModuleArticle
    $strBuffer = Controller::replaceInsertTags($objTemplate->parse(), false);
    $strBuffer = html_entity_decode($strBuffer, ENT_QUOTES, $GLOBALS['TL_CONFIG']['characterSet']);
    $strBuffer = Controller::convertRelativeUrls($strBuffer, '', true);

    // Remove form elements and JavaScript links
    $arrSearch = array
    (
      '@<form.*</form>@Us',
      '@<a [^>]*href="[^"]*javascript:[^>]+>.*</a>@Us'
    );

    $strBuffer = preg_replace($arrSearch, '', $strBuffer);

    // URL decode image paths (see contao/core#6411)
    // Make image paths absolute
    $blnOverrideRoot = false;
    $strBuffer = preg_replace_callback('@(src=")([^"]+)(")@', function ($args) use (&$blnOverrideRoot) {
      if (preg_match('@^(http://|https://)@', $args[2])) {
        return $args[1] . $args[2] . $args[3];
      }

      $path = rawurldecode($args[2]);

      if (method_exists(File::class, 'createIfDeferred')) {
        (new File($path))->createIfDeferred();
      }

      $blnOverrideRoot = true;
      return $args[1] . TL_ROOT . '/' . $path . $args[3];
    }, $strBuffer);

    if ($blnOverrideRoot) {
      $_SERVER['DOCUMENT_ROOT'] = TL_ROOT;
    }

    // Handle line breaks in preformatted text
    $strBuffer = preg_replace_callback('@(<pre.*</pre>)@Us', 'nl2br_callback', $strBuffer);

    // Default PDF export using TCPDF
    $arrSearch = array
    (
      '@<span style="text-decoration: ?underline;?">(.*)</span>@Us',
      '@(<img[^>]+>)@',
      '@(<div[^>]+block[^>]+>)@',
      '@[\n\r\t]+@',
      '@<br( /)?><div class="mod_article@',
      '@href="([^"]+)(pdf=[0-9]*(&|&amp;)?)([^"]*)"@'
    );

    $arrReplace = array
    (
      '<u>$1</u>',
      '<br>$1',
      '<br>$1',
      ' ',
      '<div class="mod_article',
      'href="$1$4"'
    );

    $strBuffer = preg_replace($arrSearch, $arrReplace, $strBuffer);

    return $strBuffer;
  }

}