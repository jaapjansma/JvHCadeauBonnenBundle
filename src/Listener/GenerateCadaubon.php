<?php

namespace JvH\CadeauBonnenBundle\Listener;

use Contao\System;
use Isotope\Model\ProductCollectionItem;
use Isotope\Model\Rule;
use JvH\CadeauBonnenBundle\Helper\NotificationHelper;
use JvH\CadeauBonnenBundle\Model\Cadeaubon;
use Isotope\Model\OrderStatus;
use Isotope\Model\ProductCollection\Order;

class GenerateCadaubon {

    /**
     * @var JvH\CadeauBonnenBundle\Helper\NotificationHelper
     */
    private $notificationHelper;

    public function __construct() {
        $this->notificationHelper = \System::getContainer()->get('jvh.cadeabonnen.notificationhelper');
    }

    /**
     * Update the payment status of the registration.
     * This will be done for every registration product in the cart.
     *
     * @param Order $order
     * @param $intOldStatus
     * @param $intNewStatus
     */
    public function PostOrderStatusUpdate(Order $order, $intOldStatus, $newStatus)
    {
        $wasPaid = false;
        if ($intOldStatus > 0) {
            $oldStatus = OrderStatus::findByPk($intOldStatus);
            $wasPaid = $oldStatus->isPaid();
        }
        if (!$newStatus instanceof OrderStatus) {
            $newStatus = OrderStatus::findByPk($newStatus);
        }

        if (!$wasPaid && $newStatus->isPaid()) {
            foreach ($order->getItems() as $item) {
                if (($product = $item->getProduct()) && $product instanceof Cadeaubon) {
                    for($i=0; $i < $item->quantity; $i++) {
                        $rule = new Rule();
                        $rule->type = 'cart';
                        $rule->label = $item->getName();
                        $rule->name = $item->getName();
                        $rule->discount = -1 * abs($item->getPrice());
                        $rule->applyTo = 'subtotal';
                        $rule->rounding = 'normal';
                        $rule->enableCode = 1;
                        $rule->limitPerMember = 0;
                        $rule->limitPerConfig = 1;
                        $rule->minSubtotal = 0;
                        $rule->maxSubtotal = 0;
                        $rule->minItemQuantity = 0;
                        $rule->maxItemQuantity = 0;
                        $rule->quantityMode = 'product_quantity';
                        $rule->configCondition = 1;
                        $rule->memberRestrictions = 'none';
                        $rule->memberCondition = 1;
                        $rule->productRestrictions = 'none';
                        $rule->productCondition = 1;
                        $rule->enabled = 1;
                        $rule->jvh_cadeaubon = 1;
                        $rule->product_collection_item_id = $item->id;
                        $rule->email = $order->getEmailRecipient();
                        $startDate = new \DateTime();
                        $rule->startDate = $startDate->getTimestamp();
                        $endDate = new \DateTime();
                        $endDate->modify('+3 year');
                        $rule->endDate = $endDate->getTimestamp();
                        $rule->code = $this->getUniqueCode($order->getId());
                        $rule->pin = $this->getPin();
                        $rule->save();
                        if ($product->isPerEmail()) {
                          $this->notificationHelper->sendCodePerEmail('jvh_cadeaubon_created', $rule, $item, $order);
                        }
                    }
                }
            }
        }
    }

    /**
     * Generates a unique code
     *
     * @see CodeGenerator::generateCode()
     * @param $orderId
     * @return string
     */
    public function getUniqueCode($orderId): string {
        do {
            $code = $this->generateCode($orderId);
            $rule = Rule::findBy(array('code = ?'), array($code));
        } while ($rule !== null);
        return $code;
    }

  /**
   * Generates a unique code
   *
   * @see CodeGenerator::generateCode()
   * @return string
   */
  public function getPin(): string {
    $numbers = "0123456789";
    $pin = '';
    for ($i = 0; $i < 4; $i++) {
      $pin .= $numbers[mt_rand(0, strlen($numbers)-1)];
    }
    return $pin;
  }

    /**
     * Generate a code like ABCD-FGHUU1234-EFGH
     *
     * The middle bit consists of the orderId. Making this code almost unique
     *
     * @param $orderId
     * @return string
     */
    public function generateCode($orderId): string {
        $numbers = "0123456789";
        $res = "";
        for ($i = 0; $i < 4; $i++) {
            $res .= $numbers[mt_rand(0, strlen($numbers)-1)];
        }
        $res .= '-';
        for($i=1; $i<=(8 - strlen($orderId)); $i++) {
            $res .= $numbers[mt_rand(0, strlen($numbers)-1)];
        }
        $res .= $orderId;
        $res .= '-';
        for ($i = 0; $i < 4; $i++) {
            $res .= $numbers[mt_rand(0, strlen($numbers)-1)];
        }
        return $res;
    }

}
