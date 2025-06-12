<?php

namespace JvH\CadeauBonnenBundle\Listener;

use Isotope\Model\ProductCollection\Order;
use Isotope\Model\ProductCollectionItem;
use Isotope\Model\ProductCollectionSurcharge\Rule;
use JvH\CadeauBonnenBundle\Helper\SnelstartVerkoopGrootboekRekening;
use JvH\CadeauBonnenBundle\Model\Cadeaubon;
use Krabo\SnelstartBundle\Connector\V2\MemorialBoekingConnector;
use Krabo\SnelstartBundle\Factory;
use Krabo\SnelstartBundle\Model\V2\Memorialboeking;
use Krabo\SnelstartBundle\Snelstart\Grootboek;
use Krabo\SnelstartBundle\Snelstart\MemorialDagboek;
use SnelstartPHP\Model\Type\BtwRegelSoort;
use SnelstartPHP\Model\Type\BtwSoort;
use SnelstartPHP\Model\V2\Boekingsregel;
use SnelstartPHP\Model\V2\Verkoopboeking;
use SnelstartPHP\Serializer\SnelstartRequestRequestSerializer;

class Snelstart {

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var MemorialDagboek
     */
    protected $dagboek;

    /**
     * @var SnelstartVerkoopGrootboekRekening
     */
    protected $verkoopCadeabonnen;

    protected $cadeauBonnenGrootboekRekening;

    public function __construct(Factory $factory, MemorialDagboek $dagboek, SnelstartVerkoopGrootboekRekening $verkoopCadeabonnen, $cadeauBonnenGrootboekRekening) {
        $this->factory = $factory;
        $this->dagboek = $dagboek;
        $this->verkoopCadeabonnen = $verkoopCadeabonnen;
        $this->cadeauBonnenGrootboekRekening = $cadeauBonnenGrootboekRekening;
    }

    /**
     * @param Boekingsregel[] $boekingsregels
     * @param ProductCollectionItem $item
     * @param $omschrijving
     * @param $btwBedrag
     * @param $bedragExBtw
     * @param Order $order
     * @param array $arrTaxes
     * @param $btwPercentages
     */
    public function GekochteCadeaubonnen(array &$boekingsRegels, ProductCollectionItem $item, $omschrijving, $btwBedrag, $bedragExBtw, Order $order, array &$arrTaxes, $btwPercentages) {
        if ($item->getProduct() instanceof Cadeaubon) {
            $btwRegelSoort = BtwRegelSoort::GEEN();
            if ($boekingsRegels[0]->getBtwSoort()->getValue() == BtwSoort::HOOG()) {
                $btwRegelSoort = BtwRegelSoort::VERKOPENHOOG();
            } elseif ($boekingsRegels[0]->getBtwSoort()->getValue() == BtwSoort::LAAG()) {
                $btwRegelSoort = BtwRegelSoort::VERKOPENLAAG();
            }

            $boekingsRegels[0]->setGrootboek($this->verkoopCadeabonnen->getGrootboekRekNrVoorCadeabonnen($order));
            $boekingsRegels[0]->setBedrag(\Money\Money::EUR($bedragExBtw + $btwBedrag));
            $boekingsRegels[0]->setBtwSoort(BtwSoort::GEEN());
            if (isset($arrTaxes[$btwRegelSoort->getValue()])) {
                $arrTaxes[$btwRegelSoort->getValue()] = $arrTaxes[$btwRegelSoort->getValue()] - $btwBedrag;
            }
        }
    }

    /**
     * @param Order $order
     * @param Verkoopboeking $verkoopboeking
     */
    public function BetaaltMetCadeaubon(Order $order, Verkoopboeking $verkoopboeking) {
        $serializer = new SnelstartRequestRequestSerializer();
        $debiteurenBoekingsRegels = [];
        foreach ($order->getSurcharges() as $surcharge) {
            if ($surcharge instanceof Rule) {
                $bedrag = (int)round($surcharge->total_price * 100);
                $bedrag = \Money\Money::EUR(abs($bedrag));
                $debiteurenBoekingsRegels[] = array(
                    'boekingId' => array(
                        'id' => $verkoopboeking->getId(),
                        'uri' => $verkoopboeking->getUri()
                    ),
                    'bedrag' => $serializer->moneyFormatToString($bedrag),
                    'omschrijving' => $order->getDocumentNumber(),
                );
            }
        }
        if (count($debiteurenBoekingsRegels)) {
            $boeking = new Memorialboeking();
            $boeking->setOmschrijving($order->getDocumentNumber());
            $boeking->setVerkoopboekingBoekingsRegels($debiteurenBoekingsRegels);
            $boeking->setDatum(new \DateTime());
            $boeking->setDagboek($this->dagboek->getDagboek($this->cadeauBonnenGrootboekRekening));
            $boekingConnector = new MemorialBoekingConnector($this->factory->getConnection());
            $boeking = $boekingConnector->addMemorialBoeking($boeking);
        }
    }

}
