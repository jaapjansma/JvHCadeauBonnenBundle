<?php

namespace JvH\CadeauBonnenBundle\Listener;

use Contao\StringUtil;
use Isotope\Model\ProductCollection\Cart;
use Isotope\Model\ProductCollection\Order;
use Isotope\Model\Rule;
use Isotope\Module\Checkout;

class UseCadaubon {

    public function preCheckout(Order $order, Checkout $module)
    {
      $changedRules = [];
      $arrRules = $this->getCadeaubonnen($order);
      if ($order->getTotal() > 0) {
        foreach ($arrRules as $rule) {
          $changedRules[$rule->id]['enabled'] = '0';
        }
      } else {
        $surchargeTotal = 0.00;
        foreach ($order->getSurcharges() as $surcharge) {
          if ($surcharge->addToTotal) {
            $surchargeTotal += $surcharge->total_price;
          }
        }
        $saldo = (float) $surchargeTotal + (float)$order->getSubtotal();
        foreach ($arrRules as $rule) {
          if ($saldo < 0 && $rule->discount > $saldo) {
            $saldo = $saldo + (-1 * $rule->discount);
            $changedRules[$rule->id]['enabled'] = '1';
          } elseif ($saldo < 0) {
            if (!strlen($rule->original_discount)) {
              $changedRules[$rule->id]['original_discount'] = $rule->discount;
            }
            $changedRules[$rule->id]['discount'] = $saldo;
            $changedRules[$rule->id]['enabled'] = '1';
            $saldo = 0;
          } else {
            $changedRules[$rule->id]['enabled'] = '0';
          }
        }
      }
      $order->jvhCadeauBonnen = $changedRules;
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
    }

    protected function getCadeaubonnen(Order $order) {
      $arrRules = [];
      $objCart = Cart::findByPk($order->source_collection_id);
      if ($objCart !== null) {
        $arrCoupons = StringUtil::deserialize($objCart->coupons);
        if (\is_array($arrCoupons) && !empty($arrCoupons)) {
          foreach ($arrCoupons as $k => $code) {
            $objRule = Rule::findOneByCouponCode($code, $objCart->getItems());

            if ($objRule && $objRule->jvh_cadeaubon && $objRule->applyTo == 'subtotal' && !$objRule->isPercentage()) {
              $arrRules[] = $objRule;
            }
          }
        }
      }
      return $arrRules;
    }
}
