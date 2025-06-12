<?php

namespace JvH\CadeauBonnenBundle\Helper;

use Isotope\Model\ProductCollection\Order;
use Krabo\SnelstartBundle\Helper\SnelstartBtw;
use Krabo\SnelstartBundle\Snelstart\Grootboek;

class SnelstartVerkoopGrootboekRekening {

    protected $grootboekreknr_nl;

    protected $grootboekreknr_eu;

    protected $grootboekreknr_wereld;

    /**
     * @var Grootboek
     */
    protected $grootboek;

    /**
     * @var SnelstartBtw
     */
    protected $btwHelper;

    public function __construct($grootboekreknr_nl, $grootboekreknr_eu, $grootboekreknr_wereld, Grootboek $grootboek, SnelstartBtw $btwHelper) {
        $this->grootboekreknr_nl = $grootboekreknr_nl;
        $this->grootboekreknr_eu = $grootboekreknr_eu;
        $this->grootboekreknr_wereld = $grootboekreknr_wereld;
        $this->grootboek = $grootboek;
        $this->btwHelper = $btwHelper;
    }

    /**
     * @param Order $order
     * @return \SnelstartPHP\Model\V2\Grootboek|null
     */
    public function getGrootboekRekNrVoorCadeabonnen(Order $order) {
        if ($this->btwHelper->isNL($order)) {
            return $this->grootboek->findGrootboekRekening($this->grootboekreknr_nl);
        } elseif ($this->btwHelper->isEU($order)) {
            return $this->grootboek->findGrootboekRekening($this->grootboekreknr_eu);
        }
        return $this->grootboek->findGrootboekRekening($this->grootboekreknr_wereld);
    }

}
