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

namespace JvH\CadeauBonnenBundle\Frontend;

use Contao\BackendTemplate;
use Contao\Input;
use Contao\Module;
use Contao\StringUtil;
use Contao\System;
use Isotope\Model\Rule;

class CadeabonChecker extends Module {

  protected $strTemplate = 'mod_jvh_cadeau_bonnen_checker';

  public function generate()
  {
    $request = System::getContainer()->get('request_stack')->getCurrentRequest();
    if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
      $objTemplate = new BackendTemplate('be_wildcard');
      $objTemplate->wildcard = '### ' . $GLOBALS['TL_LANG']['FMD']['mod_jvh_cadeau_bonnen_checker'][0] . ' ###';
      $objTemplate->title = $this->headline;
      $objTemplate->id = $this->id;
      $objTemplate->link = $this->name;
      $objTemplate->href = StringUtil::specialcharsUrl(System::getContainer()->get('router')->generate('contao_backend', array('do' => 'themes', 'table' => 'tl_module', 'act' => 'edit', 'id' => $this->id)));

      return $objTemplate->parse();
    }
    return parent::generate();
  }

  /**
   * Compile the current element
   */
  protected function compile()
  {
    if ($code = Input::get('code')) {
      $this->Template->code = $code;
      $this->Template->pin = Input::get('pin');
      if (empty($this->Template->pin)) {
        $this->Template->status = $GLOBALS['TL_LANG']['mod_jvh_cadeau_bonnen_checker']['invalid_pin'];
      } else {
        $objRule = Rule::findOneByCouponCode($code, []);
        if ($objRule && $objRule->jvh_cadeaubon && $objRule->applyTo == 'subtotal' && !$objRule->isPercentage() && strlen($objRule->pin) && $objRule->pin == Input::get('pin')) {
          $this->Template->status = sprintf($GLOBALS['TL_LANG']['mod_jvh_cadeau_bonnen_checker']['discount'], number_format(abs($objRule->discount), 2, ',', '.'));
        } else {
          $this->Template->status = $GLOBALS['TL_LANG']['mod_jvh_cadeau_bonnen_checker']['invalid'];
        }
      }
    }
  }


}