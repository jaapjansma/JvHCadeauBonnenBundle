<?php

namespace JvH\CadeauBonnenBundle\Listener;

use Contao\Environment;
use Contao\StringUtil;
use Contao\Template;
use Haste\Input\Input;
use Isotope\Frontend;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Isotope;
use Isotope\Model\ProductCollection;
use Isotope\Model\ProductCollection\Cart;
use Isotope\Model\ProductCollection\Order;
use Isotope\Model\Rule;
use Isotope\Module\Checkout;

class UseCadaubon
{

  public function addCollectionToTemplate(Template $objTemplate, array $arrItems, IsotopeProductCollection $objCollection, array $arrConfig) {
    $objConfig = Isotope::getConfig();
    $cart = null;
    if ($objCollection instanceof Cart) {
      $cart = $objCollection;
    } elseif ($objCollection instanceof Order && $objCollection->source_collection_id) {
      $cart = Cart::findByPk($objCollection->source_collection_id);
    }
    if ($cart) {
      $arrRules = $this->getCadeaubonnen($cart);
      $enhancedRules = $this->enhanceRules($arrRules, $cart);
      $surcharges = $objCollection->getSurcharges();
      foreach ($surcharges as $key => $surcharge) {
        if ($surcharge instanceof \Isotope\Model\ProductCollectionSurcharge\Rule) {
          foreach($enhancedRules as $ruleId => $enhancedRule) {
            if ($enhancedRule['available_discount_amount'] == $surcharge->total_price && $enhancedRule['original_label'] == $surcharge->label) {
              $surcharges[$key]->total_price = $enhancedRule['discount_amount'];
              $surcharges[$key]->code = $enhancedRule['code'];
              $surcharges[$key]->label = $enhancedRule['label'];
            } elseif ($enhancedRule['original_label'] == $surcharge->label) {
              $surcharges[$key]->label = $enhancedRule['label'];
              $surcharges[$key]->code = $enhancedRule['code'];
            }
          }
        }
      }
      $surchargeItems= Frontend::formatSurcharges($surcharges, $objConfig->currency);
      foreach ($surchargeItems as $key => $surchargeItem) {
        if (!empty($surchargeItem['code'])) {
          $surchargeItems[$key]['remove_link'] = Environment::get('request') . '?remove_coupon=' . $key;
        }
      }
      if ($key = Input::get('remove_coupon')) {
        if (isset($surchargeItems[$key]['code'])) {
          $coupons = StringUtil::deserialize($objCollection->coupons);
          if (!\is_array($coupons)) {
            $coupons = array();
          }
          foreach($coupons as $index => $coupon) {
            if ($coupon == $surchargeItems[$key]['code']) {
              unset($coupons[$index]);
            }
          }
        }
        $objCollection->coupons = serialize($coupons);
        $objCollection->save();
        $url = str_replace('remove_coupon='.$key, '', Environment::get('uri'));
        \Controller::redirect($url);
      }
      $objTemplate->surcharges = $surchargeItems;
    }
  }

    public function preCheckout(Order $order, Checkout $module)
    {
      $arrRules = [];
      $objCart = Cart::findByPk($order->source_collection_id);
      if ($objCart !== null) {
        $arrRules = $this->getCadeaubonnen($objCart);
      }
      $order->jvhCadeauBonnen = $this->enhanceRules($arrRules, $order);;
    }

    public function postCheckout(Order $order, array $arrTokens) {
      $settings = $order->settings;
      if (is_string($settings)) {
        $settings = StringUtil::deserialize($settings);
      }
      if (!isset($settings['jvhCadeauBonnen'])) {
        $settings['jvhCadeauBonnen'] = [];
      }
      foreach ($settings['jvhCadeauBonnen'] as $ruleId => $fields) {
        $rule = Rule::findByPk($ruleId);
        foreach ($fields as $f => $v) {
          $rule->$f = $v;
        }
        $rule->save();
      }

      $surcharges = $order->getSurcharges();
      foreach ($surcharges as $key => $surcharge) {
        if ($surcharge instanceof \Isotope\Model\ProductCollectionSurcharge\Rule) {
          foreach($settings['jvhCadeauBonnen'] as $ruleId => $enhancedRule) {
            if ($enhancedRule['available_discount_amount'] == $surcharge->total_price && $enhancedRule['original_label'] == $surcharge->label) {
              $surcharge->total_price = $enhancedRule['discount_amount'];
              $surcharge->label = $enhancedRule['label'];
              $surcharge->save();
            } elseif ($enhancedRule['original_label'] == $surcharge->label) {
              $surcharge->label = $enhancedRule['label'];
              $surcharge->save();
            }
          }
        }
      }
    }

    protected function enhanceRules(array $arrRules, ProductCollection $objCollection): array {
      $changedRules = [];
      if ($objCollection->getTotal() > 0) {
        foreach ($arrRules as $rule) {
          $changedRules[$rule->id]['code'] = $rule->code;
          $changedRules[$rule->id]['enabled'] = '0';
          $changedRules[$rule->id]['original_label'] = $rule->label;
          if (empty($rule->label)) {
            $changedRules[$rule->id]['original_label'] = $rule->name;
          }
          $changedRules[$rule->id]['label'] = sprintf($GLOBALS['TL_LANG']['jvh_cadeau_bonnen_surcharge_label'], number_format(abs($rule->discount), 2, ',', '.'));
        }
      } else {
        $surchargeTotal = 0.00;
        foreach ($objCollection->getSurcharges() as $surcharge) {
          if ($surcharge->addToTotal) {
            $surchargeTotal += $surcharge->total_price;
          }
        }
        $saldo = (float) $surchargeTotal + (float)$objCollection->getSubtotal();
        foreach ($arrRules as $rule) {
          $changedRules[$rule->id]['original_label'] = $rule->label;
          if (empty($rule->label)) {
            $changedRules[$rule->id]['original_label'] = $rule->name;
          }
          $changedRules[$rule->id]['label'] = sprintf($GLOBALS['TL_LANG']['jvh_cadeau_bonnen_surcharge_label'], number_format(abs($rule->discount), 2, ',', '.'));
          $changedRules[$rule->id]['code'] = $rule->code;
          $changedRules[$rule->id]['available_discount_amount'] = (float) $rule->discount;
          if ($saldo < 0 && $rule->discount > $saldo) {
            $saldo = $saldo + (-1 * $rule->discount);
            $changedRules[$rule->id]['enabled'] = '1';
            $changedRules[$rule->id]['discount_amount'] = $rule->discount;
            $changedRules[$rule->id]['limitPerConfig'] = $rule->limitPerConfig;
            if ($rule->limitPerConfig > 0) {
              $changedRules[$rule->id]['limitPerConfig']++;
            }
          } elseif ($saldo < 0) {
            if (!strlen($rule->original_discount)) {
              $changedRules[$rule->id]['original_discount'] = $rule->discount;
            }
            $changedRules[$rule->id]['discount'] = $saldo;
            $changedRules[$rule->id]['discount_amount'] = $rule->discount - $saldo;
            $changedRules[$rule->id]['limitPerConfig'] = $rule->limitPerConfig;
            if ($rule->limitPerConfig > 0) {
              $changedRules[$rule->id]['limitPerConfig']++;
            }
            $changedRules[$rule->id]['enabled'] = '1';
            $saldo = 0;
          } else {
            $changedRules[$rule->id]['original_discount'] = $rule->discount;
            $changedRules[$rule->id]['discount_amount'] = $rule->discount;
            $changedRules[$rule->id]['enabled'] = '0';
          }
        }
      }
      return $changedRules;
    }

    protected function getCadeaubonnen(Cart $objCart) {
      $arrRules = [];
      $arrCoupons = StringUtil::deserialize($objCart->coupons);
      if (\is_array($arrCoupons) && !empty($arrCoupons)) {
        foreach ($arrCoupons as $k => $code) {
          $objRule = Rule::findOneByCouponCode($code, $objCart->getItems());

          if ($objRule && $objRule->jvh_cadeaubon && $objRule->applyTo == 'subtotal' && !$objRule->isPercentage()) {
            $arrRules[] = $objRule;
          }
        }
      }
      return $arrRules;
    }
}
