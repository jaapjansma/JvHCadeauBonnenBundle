<?php

namespace JvH\CadeauBonnenBundle\Model;

use Contao\Model\Collection;
use Isotope\Interfaces\IsotopePrice;
use Isotope\Isotope;
use Isotope\Model\Product;
use Isotope\Model\TaxClass;
use Krabo\CalendarEventBookingIsotopeBundle\Model\CalEvtBookingPriceModel;

class CadeaubonPrice implements IsotopePrice {

    /**
     * @var float
     */
    protected $cadeaubonBedrag = 0.00;

    /**
     * @var Cadeaubon
     */
    protected $product;

    /**
     * CadeaubonPrice constructor.
     * @param float $cadeaubonBedrag
     * @param Cadeaubon $product
     */
    public function __construct($cadeaubonBedrag, Cadeaubon $product) {
        $this->cadeaubonBedrag = $cadeaubonBedrag;
        $this->product = $product;
    }

    /**
     * Return true if more than one price is available
     *
     * @return bool
     */
    public function hasTiers()
    {
        return false;
    }

    /**
     * Return price
     *
     * @param int $intQuantity
     * @param array $arrOptions
     *
     * @return float
     */
    public function getAmount($intQuantity = 1, array $arrOptions = array())
    {
        return Isotope::calculatePrice($this->cadeaubonBedrag, $this, 'price', 0, null, $arrOptions);
    }

    /**
     * Return original price
     *
     * @param int $intQuantity
     * @param array $arrOptions
     *
     * @return float
     */
    public function getOriginalAmount($intQuantity = 1, array $arrOptions = array())
    {
        return Isotope::calculatePrice($this->cadeaubonBedrag, $this, 'original_price', 0, null, $arrOptions);
    }

    /**
     * Return net price (without taxes)
     *
     * @param int $intQuantity
     * @param array $arrOptions
     *
     * @return float
     */
    public function getNetAmount($intQuantity = 1, array $arrOptions = array())
    {
        return Isotope::calculatePrice($this->cadeaubonBedrag, $this, 'net_price', 0, null, $arrOptions);
    }

    /**
     * Return gross price (with all taxes)
     *
     * @param int $intQuantity
     * @param array $arrOptions
     *
     * @return float
     */
    public function getGrossAmount($intQuantity = 1, array $arrOptions = array())
    {
        return Isotope::calculatePrice($this->cadeaubonBedrag, $this, 'gross_price', 0, null, $arrOptions);
    }

    /**
     * Generate price for HTML rendering
     *
     * @param bool  $blnShowTiers
     * @param int   $intQuantity
     * @param array $arrOptions
     *
     * @return string
     */
    public function generate($blnShowTiers = false, $intQuantity = 1, array $arrOptions = array())
    {
        $fltPrice = $this->getAmount($intQuantity, $arrOptions);
        $strPrice = Isotope::formatPriceWithCurrency($fltPrice);
        $fltOriginalPrice = $this->getOriginalAmount($intQuantity, $arrOptions);
        if ($fltPrice < $fltOriginalPrice) {
            $strOriginalPrice = Isotope::formatPriceWithCurrency($fltOriginalPrice);
            // @deprecated remove <strike>, should be a CSS setting
            return '<div class="original_price"><strike>' . $strOriginalPrice . '</strike></div><div class="price">' . $strPrice . '</div>';
        }

        return $strPrice;
    }

    /**
     * Lazy load related records
     *
     * @param string $strKey     The property name
     * @param array  $arrOptions An optional options array
     *
     * @return static|Collection|null The model or a model collection if there are multiple rows
     *
     * @throws \Exception If $strKey is not a related field
     */
    public function getRelated($strKey, array $arrOptions=array()) {
        if ($strKey == 'tax_class') {
            return null;
        }
        if ($strKey == 'pid') {
            if ($this->product) {
                return $this->product;
            }
        }
        throw new \Exception("Field $strKey does not seem to be related");
    }


}
