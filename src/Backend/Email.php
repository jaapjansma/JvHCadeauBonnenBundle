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

namespace JvH\CadeauBonnenBundle\Backend;

use Contao\Backend;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Message;
use Isotope\Model\ProductCollection\Order;
use Isotope\Model\ProductCollectionItem;
use Isotope\Model\Rule;
use JvH\CadeauBonnenBundle\Helper\NotificationHelper;

class Email extends Backend {

  /**
   * @var NotificationHelper
   */
  private $notificationHelper;

  public function __construct() {
    parent::__construct();
    $this->notificationHelper = \System::getContainer()->get('jvh.cadeabonnen.notificationhelper');
  }

  public function sendEmail(DataContainer $dc) {
    $rule = Rule::findByPk($dc->id);
    $item = null;
    $order = null;
    if ($rule && $rule->product_collection_item_id) {
      $item = ProductCollectionItem::findByPk($rule->product_collection_item_id);
      if ($item) {
        $order = Order::findByPk($item->pid);
      }
    }
    if ($rule->email) {
      if ($rule->product_collection_item_id && $order) {
        $count = $this->notificationHelper->sendCodePerEmail('jvh_cadeaubon_created', $rule, $item, $order);
      } else {
        $count = $this->notificationHelper->sendCodePerEmail('jvh_cadeaubon_email', $rule, $item, $order);
      }
      Message::addInfo(sprintf($GLOBALS['TL_LANG']['tl_iso_rule']['email_send'], $count));
    }
    $url = str_replace('&key=send_email', '', \Environment::get('request'));
    $url = str_replace('&id='.$dc->id, '', $url);
    Controller::redirect($url);
  }

}