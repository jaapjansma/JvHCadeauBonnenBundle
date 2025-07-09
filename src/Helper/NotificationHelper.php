<?php

namespace JvH\CadeauBonnenBundle\Helper;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\StringUtil;
use Contao\System;
use Haste\Util\Format;
use Isotope\Isotope;
use Isotope\Model\Address;
use Isotope\Model\Document;
use Isotope\Model\ProductCollection\Order;
use Isotope\Model\ProductCollectionItem;
use Isotope\Model\Rule;

class NotificationHelper
{

  /**
   * @var ContaoFramework
   */
  private $framework;

  public function __construct(ContaoFramework $framework)
  {
    $this->framework = $framework;
  }

  public function sendCodePerEmail(string $notificationType, Rule $rule, ProductCollectionItem $item = null, Order $order = null): int
  {
    $count = 0;
    // Load language file
    System::loadLanguageFile('tl_member');
    $delimiter = ",";

    $arrTokens = array();
    $arrTokens['recipient_email'] = $rule->email;
    $arrTokens['code'] = $rule->code;
    $arrTokens['pin'] = $rule->pin;
    $arrTokens['discount'] = str_replace(",00", ",-", Isotope::formatPrice(abs($rule->discount)));
    $arrTokens['startDate'] = '';
    $arrTokens['endDate'] = '';
    if ($rule->startDate) {
      $arrTokens['startDate'] = date('d-m-Y', $rule->startDate);
    }
    if ($rule->endDate) {
      $arrTokens['endDate'] = date('d-m-Y', $rule->endDate);
    }

    $row = $rule->row();
    foreach ($row as $k => $v) {
      $arrTokens = $this->flatten($v, 'rule_' . $k, $arrTokens, $delimiter);
    }

    if ($order) {
      // Prepare tokens for event member and use "member_" as prefix
      if ($order->getMember()) {
        $row = $order->getMember()->row();
        foreach ($row as $k => $v) {
          $arrTokens = $this->flatten($v, 'member_' . $k, $arrTokens, $delimiter);
        }
      }

      $row = $order->row();
      foreach ($row as $k => $v) {
        $arrTokens = $this->flatten($v, 'order_' . $k, $arrTokens, $delimiter);
      }

      // Vul de form_ tokens.
      if (isset($order->settings) && !is_array($order->settings)) {
        $arrSettings = \StringUtil::deserialize($order->settings, true);
      } else {
        $arrSettings = $order->settings;
      }
      if (isset($arrSettings['email_data'])) {
        foreach ($arrSettings['email_data'] as $k => $v) {
          // Verwijder de form_ van het veld naam.
          // Bijv. form_opmerkingen is het veld, token is dan form__form_opmerkingen
          // we willen juist form_opmerkingen.
          if (strpos($k, 'form_') === 0) {
            $k = substr($k, strlen('form_'));
          }
          $arrTokens = $this->flatten($v, 'form_' . $k, $arrTokens, $delimiter);
        }
      }

      // Add billing address fields
      if (($objAddress = $order->getBillingAddress()) !== null) {
        foreach ($objAddress->row() as $k => $v) {
          $arrTokens = $this->flatten($v, 'billing_address_' . $k, $arrTokens, $delimiter);
        }
      }

      // Add shipping address fields
      if (($objAddress = $order->getShippingAddress()) !== null) {
        foreach ($objAddress->row() as $k => $v) {
          $arrTokens = $this->flatten($v, 'shipping_address_' . $k, $arrTokens, $delimiter);
        }
        $objConfig = $order->getRelated('config_id') ?: Isotope::getConfig();
        Isotope::setConfig($objConfig);
        $arrTokens['shipping_address'] = $objAddress->generate($objConfig->getShippingFieldsConfig());
      }
    }

    if ($item) {
      $row = $item->row();
      foreach ($row as $k => $v) {
        $arrTokens = $this->flatten($v, 'product_' . $k, $arrTokens, $delimiter);
      }
    }

    $objNotificationCollection = \NotificationCenter\Model\Notification::findByType($notificationType);
    if (null !== $objNotificationCollection) {
      $objNotificationCollection->reset();
      while ($objNotificationCollection->next()) {
        $arrTokens['document'] = '';
        $objNotification = $objNotificationCollection->current();
        // Generate and "attach" document
        /** @var \Isotope\Interfaces\IsotopeDocument $objDocument */
        if ($objNotification->iso_document > 0
          && (($objDocument = Document::findByPk($objNotification->iso_document)) !== null)
        ) {
          $strFilePath           = \JvH\CadeauBonnenBundle\Helper\Document::outputToFile($objDocument, $arrTokens, TL_ROOT . '/system/tmp');
          $arrTokens['document'] = str_replace(TL_ROOT . '/', '', $strFilePath);
        }

        $objNotification->send($arrTokens);
        $count++;
      }
    }
    return $count;
  }

  /**
   * Flatten input data, Simple Tokens can't handle arrays.
   *
   * @param mixed $varValue
   * @param string $strKey
   * @param string $strPattern
   */
  private function flatten($varValue, $strKey, array $arrData, $strPattern = ', ')
  {
    /** @var StringUtil $stringUtilAdapter */
    $stringUtilAdapter = $this->framework->getAdapter(StringUtil::class);

    if (!empty($varValue) && !\is_array($varValue) && \is_string($varValue) && \strlen($varValue) > 3 && \is_array($stringUtilAdapter->deserialize($varValue))) {
      $varValue = $stringUtilAdapter->deserialize($varValue);
    }

    if (\is_object($varValue)) {
      return $arrData;
    }

    if (!\is_array($varValue)) {
      $arrData[$strKey] = $varValue;

      return $arrData;
    }

    $blnAssoc = array_is_assoc($varValue);

    $arrValues = [];

    foreach ($varValue as $k => $v) {
      if ($blnAssoc || \is_array($v)) {
        $arrData = $this->flatten($v, $strKey . '_' . $k, $arrData);
      } else {
        $arrData[$strKey . '_' . $v] = '1';
        $arrValues[] = $v;
      }
    }

    $arrData[$strKey] = implode($strPattern, $arrValues);

    return $arrData;
  }

}
