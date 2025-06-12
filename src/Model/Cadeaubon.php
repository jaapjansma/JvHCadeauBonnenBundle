<?php

namespace JvH\CadeauBonnenBundle\Model;

use Contao\DcaLoader;
use Haste\Units\Mass\Weight;
use Haste\Units\Mass\WeightAggregate;
use Isotope\Interfaces\IsotopeProductCollection;
use Isotope\Isotope;
use Isotope\Model\Product;
use Isotope\Model\Product\Standard;

class Cadeaubon extends Standard implements WeightAggregate {


    /**
     * Returns true if the product is available
     * ALMOST THE SAME AS THE PARENT, EXCEPT WE DON'T CHECK FOR PRICE
     *
     * @param IsotopeProductCollection $objCollection
     *
     * @return bool
     */
    public function isAvailableForCollection(IsotopeProductCollection $objCollection)
    {
        if ($objCollection->isLocked()) {
            return true;
        }

        if (BE_USER_LOGGED_IN !== true && !$this->isPublished()) {
            return false;
        }

        // Show to guests only
        if ($this->arrData['guests'] && $objCollection->member > 0 && BE_USER_LOGGED_IN !== true && !$this->arrData['protected']) {
            return false;
        }

        // Protected product
        if (BE_USER_LOGGED_IN !== true && $this->arrData['protected']) {
            if ($objCollection->member == 0) {
                return false;
            }

            $groups       = deserialize($this->arrData['groups']);
            $memberGroups = deserialize($objCollection->getRelated('member')->groups);

            if (!is_array($groups) || empty($groups) || !is_array($memberGroups) || empty($memberGroups) || !count(array_intersect($groups, $memberGroups))) {
                return false;
            }
        }

        // Check that the product is in any page of the current site
        if (count(\Isotope\Frontend::getPagesInCurrentRoot($this->getCategories(), $objCollection->getRelated('member'))) == 0) {
            return false;
        }

        return true;
    }


    /**
     * Return a widget object based on a product attribute's properties
     *
     * @param string $strField
     * @param array  $arrVariantOptions
     * @param array  $arrAjaxOptions
     *
     * @return string
     */
    protected function generateProductOptionWidget($strField, &$arrVariantOptions, &$arrAjaxOptions, &$objWidget = null)
    {
        DcaLoader::loadDataContainer(Product::getTable());
        $GLOBALS['TL_DCA'][Product::getTable()]['fields']['cadeaubon_bedrag']['default'] =
            $GLOBALS['TL_DCA'][Product::getTable()]['fields']['cadeau_bedrag']['default'] ?: Isotope::formatPrice($this->getPrice()->getAmount());

        return parent::generateProductOptionWidget($strField, $arrVariantOptions, $arrAjaxOptions);
    }

    public function getWeight()
    {
        if (!isset($this->arrData['shipping_weight'])) {
            return new Weight(0, 'kg');
        }
        return Weight::createFromTimePeriod($this->arrData['shipping_weight']);
    }

    public function getPrice(IsotopeProductCollection $objCollection = null)
    {
        $price = parent::getPrice($objCollection);
        if (null === $price) {
            return new CadeaubonPrice($this->cadeaubon_bedrag, $this);
        }
        return $price;
    }


}
