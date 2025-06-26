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

class ProductCollection {

  public function generateJvhCadeaubonnen($dc) {
    $db = \Database::getInstance();
    $rules = $db->prepare("SELECT tl_iso_rule.* FROM tl_iso_rule INNER JOIN tl_iso_rule_usage ON tl_iso_rule.id = tl_iso_rule_usage.pid WHERE tl_iso_rule_usage.order_id = ?")->execute([$dc->id]);
    $results = '';
    if ($rules->count() > 0) {
      $results .= '<table class="tl_show">';
    }
    while($rules->next()) {
      $label = $rules->label;
      if (empty($label)) {
        $label = $rules->name;
      }
      $results .=  '<tr><td class="label">' . $label .'</td>';
      if (!empty($rules->code)) {
        $results .= '<td>' . $rules->code . '</td>';
      }
      $results .= '</tr>';
    }
    if ($rules->count() > 0) {
      $results .= '</table>';
    }
    return $results;
  }

}