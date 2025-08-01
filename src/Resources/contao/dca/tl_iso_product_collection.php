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

$GLOBALS['TL_DCA']['tl_iso_product_collection']['palettes']['default'] .= ';{jvh_cadeaubonnen_legend},jvh_cadeaubon';
$GLOBALS['TL_DCA']['tl_iso_product_collection']['fields']['jvh_cadeaubon'] = [
  'input_field_callback'  => array('JvH\CadeauBonnenBundle\Backend\ProductCollection', 'generateJvhCadeaubonnen'),
  'eval'                  => array('doNotShow'=>true),
];