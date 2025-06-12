<?php

namespace JvH\CadeauBonnenBundle\Listener;

use Isotope\Interfaces\IsotopePrice;

class CalculatePrice {

    /**
     * Calculate the price of a gift product
     *
     * Namespace:	Isotope
     * Class:		Isotope
     * Method:		calculatePrice
     * Hook:		$GLOBALS['ISO_HOOKS']['calculatePrice']
     *
     * @access		public
     * @param		mixed
     * @return		float
     */
    public function run($fltPrice, $objSource, $strField, $intTaxClass, $arrOptions)
    {
        if ( !($objSource instanceof IsotopePrice) ||
            ($strField !== 'price' && $strField !== 'low_price') ||
            !is_array($arrOptions) ||
            !$arrOptions['cadeaubon_bedrag']
        )
        {
            return $fltPrice;
        }

        $fltPrice += (float) $arrOptions['cadeaubon_bedrag'];
        return $fltPrice;
    }

}
