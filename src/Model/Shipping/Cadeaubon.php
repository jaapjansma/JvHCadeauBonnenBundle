<?php

namespace JvH\CadeauBonnenBundle\Model\Shipping;

use Isotope\Isotope;
use Isotope\Model\Shipping\Flat;


class Cadeaubon extends Flat {
    /**
     * @inheritdoc
     *
     * @throws \InvalidArgumentException on unknown quantity mode
     * @throws \UnexpectedValueException on unknown product type condition
     */
    public function isAvailable()
    {

        $cadeaubonInCart = false;
        $otherProductsIncart = false;
        foreach(Isotope::getCart()->getItems() as $item) {
            if ($item->getProduct() && $item->getProduct() instanceof \JvH\CadeauBonnenBundle\Model\Cadeaubon) {
                $cadeaubonInCart = true;
            } else {
                $otherProductsIncart = true;
            }
        }
        if ($cadeaubonInCart && !$otherProductsIncart) {
            return parent::isAvailable();
        }
        return false;
    }


}