<?php
/**
 * Trive Fiskal API Library.
 *
 * @category  Trive
 * @package   Trive_Fiskal
 * @copyright 2017 Trive d.o.o (http://trive.digital)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link      http://trive.digital
 */

namespace Trive\Fiskal\Model\Client;

use Trive\Fiskal\Model\Client;
use Trive\Fiskal\Model\Config;
use Trive\Fiskal\Api\InvoiceRepositoryInterface as FiskalInvoiceRepositoryInterface;
use Trive\Fiskal\Api\SequenceRepositoryInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\ClassModel as TaxClassModel;
use Magento\Tax\Api\TaxClassManagementInterface;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Tax\Api\Data\TaxClassInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Psr\Log\LoggerInterface;
use Trive\Fiskal\Api\Data\InvoiceInterface as FiskalInvoiceInterface;
use Trive\FiskalAPI\Invoice\Invoice as FiskalInvoice;
use Trive\FiskalAPI\Invoice\Fee;
use Trive\FiskalAPI\Invoice\InvoiceNumber;
use Trive\FiskalAPI\Invoice\TaxRate;
use Trive\Fiskal\Api\Data\SequenceInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class Invoice extends Client
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FiskalInvoiceRepositoryInterface
     */
    protected $fiskalInvoiceRepository;

    /**
     * @var SequenceRepositoryInterface
     */
    protected $sequenceRepository;

    /**
     * Invoice repository.
     *
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * Creditmemo repository.
     *
     * @var CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * Invoice sender.
     *
     * @var InvoiceManagementInterface
     */
    protected $invoiceManagementInterface;

    /**
     * Creditmemo sender.
     *
     * @var CreditmemoManagementInterface
     */
    protected $creditmemoManagementInterface;

    /**
     * Tax helper.
     *
     * @var TaxHelper
     */
    protected $taxHelper;

    /**
     * Date.
     *
     * @var DateTime
     */
    protected $date;

    /**
     * @var TaxClassRepositoryInterface
     */
    protected $taxClassRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $pdvTaxRates = [];

    /**
     * @var array
     */
    protected $pnpTaxRates = [];

    /**
     * @var array
     */
    protected $otherTaxRates = [];

    /**
     * @var array
     */
    protected $fees = [];

    /**
     * @var array
     */
    protected $storeTaxClasses = [];

    /**
     * Reset all rax rates and fees
     *
     * @return $this
     */
    private function resetTaxRatesAndFees()
    {
        $this->pdvTaxRates = [];
        $this->pnpTaxRates = [];
        $this->otherTaxRates = [];
        $this->fees = [];

        return $this;
    }

    /**
     * Client constructor.
     *
     * @param Config                           $config
     * @param FiskalInvoiceRepositoryInterface $fiskalInvoiceRepository
     * @param SequenceRepositoryInterface      $sequenceRepository
     * @param InvoiceRepositoryInterface       $invoiceRepository
     * @param CreditmemoRepositoryInterface    $creditmemoRepository
     * @param InvoiceManagementInterface       $invoiceManagementInterface
     * @param CreditmemoManagementInterface    $creditmemoManagementInterface
     * @param TaxHelper                        $taxHelper
     * @param TaxClassRepositoryInterface      $taxClassRepository
     * @param FilterBuilder                    $filterBuilder
     * @param SearchCriteriaBuilder            $searchCriteriaBuilder
     * @param LoggerInterface                  $logger
     * @param array                            $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Config $config,
        FiskalInvoiceRepositoryInterface $fiskalInvoiceRepository,
        SequenceRepositoryInterface $sequenceRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        CreditmemoRepositoryInterface $creditmemoRepository,
        InvoiceManagementInterface $invoiceManagementInterface,
        CreditmemoManagementInterface $creditmemoManagementInterface,
        DateTime $date,
        TaxHelper $taxHelper,
        TaxClassRepositoryInterface $taxClassRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        $data = []
    ) {
        $this->config = $config;
        $this->fiskalInvoiceRepository = $fiskalInvoiceRepository;
        $this->sequenceRepository = $sequenceRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->invoiceManagementInterface = $invoiceManagementInterface;
        $this->creditmemoManagementInterface = $creditmemoManagementInterface;
        $this->date = $date;
        $this->taxHelper = $taxHelper;
        $this->taxClassRepository = $taxClassRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;

        parent::__construct($config, $logger, $data);
    }

    /**
     * Extract ZKI from response
     *
     * @param string $response
     *
     * @return string
     */
    public function extractZki($response)
    {
        $document = new \DOMDocument();
        $document->loadXML($response);
        $document->C14N();

        $zkiElement = $document->getElementsByTagName('ZastKod')->item(0);

        return $zkiElement->nodeValue;
    }

    /**
     * Extract JIR from response
     *
     * @param string $response
     *
     * @return string
     */
    public function extractJir($response)
    {
        $document = new \DOMDocument();
        $document->loadXML($response);
        $document->C14N();

        $jirElement = $document->getElementsByTagName('Jir')->item(0);

        return $jirElement->nodeValue;
    }

    /**
     * Get invoice number
     *
     * @param $invoiceIncrementId
     * @param $locationCode
     * @param $paymentDeviceCode
     *
     * @return InvoiceNumber
     */
    private function getInvoiceNumber($invoiceIncrementId, $locationCode, $paymentDeviceCode)
    {
        return new InvoiceNumber($invoiceIncrementId, $locationCode, $paymentDeviceCode);
    }

    /**
     * Get Tax Rate
     *
     * @param $rate
     * @param $baseValue
     * @param $value
     * @param $name
     *
     * @return TaxRate
     */
    private function getTaxRate($rate, $baseValue, $value, $name = null)
    {
        return new TaxRate($rate, $baseValue, $value, $name);
    }

    /**
     * Add PDV tax rate
     *
     * @param $rate
     * @param $baseValue
     * @param $value
     */
    private function addPdvTaxRate($rate, $baseValue, $value)
    {
        $rate = $this->getTaxRate($rate, $baseValue, $value);
        $this->pdvTaxRates[] = $rate;
    }

    /**
     * Get PDV tax rates
     *
     * @return array
     */
    public function getPdvTaxRates()
    {
        return $this->pdvTaxRates;
    }

    /**
     * Add PNP tax rate
     *
     * @param $rate
     * @param $baseValue
     * @param $value
     */
    private function addPnpTaxRate($rate, $baseValue, $value)
    {
        $rate = $this->getTaxRate($rate, $baseValue, $value);
        $this->pnpTaxRates[] = $rate;
    }

    /**
     * Get PNP tax rates
     *
     * @return array
     */
    public function getPnpTaxRates()
    {
        return $this->pnpTaxRates;
    }

    /**
     * Add other tax rate
     *
     * @param $rate
     * @param $baseValue
     * @param $value
     * @param $name
     */
    private function addOtherTaxRate($rate, $baseValue, $value, $name)
    {
        $rate = $this->getTaxRate($rate, $baseValue, $value, $name);
        $this->otherTaxRates[] = $rate;
    }

    /**
     * Get other tax rates
     *
     * @return array
     */
    public function getOtherTaxRates()
    {
        return $this->otherTaxRates;
    }

    /**
     * Add fee
     *
     * @param $name
     * @param $name
     */
    private function addFee($name, $value)
    {
        $fee = new Fee($name, $value);
        $this->fees[] = $fee;
    }

    /**
     * Get fees
     *
     * @return array
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * Get invoice sequence by location code
     *
     * @param $locationCode
     *
     * @return mixed|null|SequenceInterface
     */
    public function getSequence($locationCode)
    {
        $this->searchCriteriaBuilder->addFilter(
            SequenceInterface::LOCATION_CODE,
            $locationCode
        )->addFilter(
            SequenceInterface::YEAR,
            $this->getCurrentYear()
        )->setCurrentPage(1)->setPageSize(1);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->sequenceRepository->getList($searchCriteria)->getItems();
        $sequence = array_shift($searchResults);

        return (null == $sequence) ? null : $sequence;
    }

    /**
     * Save invoice sequence
     *
     * @param int               $increment
     * @param SequenceInterface $sequence
     *
     * @return void
     */
    public function saveSequence($increment, $sequence)
    {
        $sequence->setIncrement($increment);
        $this->sequenceRepository->save($sequence);
    }

    /**
     * Get store tax classes
     *
     * @return array
     */
    public function getStoreTaxClasses()
    {
        if (empty($this->storeTaxClasses)) {
            $filter = $this->filterBuilder->setField(TaxClassModel::KEY_TYPE)->setValue(
                TaxClassManagementInterface::TYPE_PRODUCT
            )->create();
            $searchCriteria = $this->searchCriteriaBuilder->addFilters([$filter])->create();
            $searchResults = $this->taxClassRepository->getList($searchCriteria);

            $storeTaxClasses = [];
            foreach ($searchResults->getItems() as $taxClass) {
                /** @var TaxClassInterface $taxClass */
                $storeTaxClasses[$taxClass->getClassId()] = $taxClass;
            }

            $this->storeTaxClasses = $storeTaxClasses;
        }

        return $this->storeTaxClasses;
    }

    /**
     * Send invoice/creditmemo email to customer
     *
     * @param $object
     * @param $type
     */
    public function sendEmail($object, $type)
    {
        if ($type == FiskalInvoiceInterface::ENTITY_TYPE_INVOICE) {
            if ( $this->config->getSendInvoiceEmail() ) {
                $this->invoiceManagementInterface->notify($object->getId());
            }
        } elseif ($type == FiskalInvoiceInterface::ENTITY_TYPE_CREDITMEMO) {
            if ( $this->config->getSendCreditmemoEmail() ) {
                $this->creditmemoManagementInterface->notify($object->getId());
            }
        }
    }

    /**
     * Save order comment
     *
     * @param InvoiceInterface | CreditmemoInterface $object
     * @param FiskalInvoiceInterface $fiskalInvoice
     *
     * @return void
     */
    public function saveOrderComment($object, $fiskalInvoice)
    {
        $type = $fiskalInvoice->getEntityType();
        $message = sprintf('[Fiskal] %s sync finished.', ucfirst($type));

        $status = false;
        if ($type == FiskalInvoiceInterface::ENTITY_TYPE_INVOICE) {
            $status = $this->config->getInvoiceTriggerStatusAfter();
        } elseif ($type == FiskalInvoiceInterface::ENTITY_TYPE_CREDITMEMO) {
            $status = $this->config->getCreditmemoTriggerStatusAfter();
        }

        $order = $object->getOrder();

        $history = $order->addStatusHistoryComment($message, $status);
        $history->setIsCustomerNotified(false);
        $history->save();

        $order->save();
    }

    /**
     * Save invoice
     *
     * @param FiskalInvoiceInterface $fiskalInvoice
     *
     * @return FiskalInvoiceInterface $fiskalInvoice
     */
    public function saveRequest($fiskalInvoice)
    {
        $successful = false;
        $this->config->getPaymentMapping();
        $this->config->getTaxMapping();

        $sequence = $this->getSequence($fiskalInvoice->getLocationCode());
        if ($sequence && $sequence->getId()) {
            $increment = $sequence->getIncrement() + 1;
            $invoiceNumber = $this->getInvoiceNumber(
                $increment,
                $fiskalInvoice->getLocationCode(),
                $fiskalInvoice->getPaymentDeviceCode()
            );

            $preparedInvoice = null;
            switch ($fiskalInvoice->getEntityType()) {
                case FiskalInvoiceInterface::ENTITY_TYPE_INVOICE:
                    $object = $this->invoiceRepository->get($fiskalInvoice->getEntityId());
                    $preparedInvoice = $this->prepareInvoice($object, $invoiceNumber);
                    break;
                case FiskalInvoiceInterface::ENTITY_TYPE_CREDITMEMO:
                    $object = $this->creditmemoRepository->get($fiskalInvoice->getEntityId());
                    $preparedInvoice = $this->prepareCreditmemo($object, $invoiceNumber);
                    break;
                default:
                    break;
            }

            if ($preparedInvoice) {
                $response = $this->invoiceRequest($preparedInvoice);
                if ($response->getSuccessful()) {
                    $fiskalInvoice->setJir($this->extractJir($response->getResponse()))
                                  ->setZki(
                                      $this->extractZki($response->getRequest())
                                  )
                                  ->setIncrementId($increment)
                                  ->setInvoiceNumber($invoiceNumber->getFullInvoiceNumber())
                                  ->setSyncedAt($this->getCurrentDateTime());
                    $this->saveSequence($increment, $sequence);
                    $successful = true;
                } else {
                    $fiskalInvoice->setErrorMessage($response->getResponse());
                }
            }
            $this->fiskalInvoiceRepository->save($fiskalInvoice);

            if (isset($object) && $successful) {
                $this->saveOrderComment($object, $fiskalInvoice);
                $this->sendEmail($object, $fiskalInvoice->getEntityType());
            }
        }

        return $fiskalInvoice;
    }

    /**
     * Prepare invoice for request
     *
     * @param InvoiceInterface $invoice
     * @param string           $invoiceNumber
     *
     * @return FiskalInvoice
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function prepareInvoice($invoice, $invoiceNumber)
    {
        $prefix = 1;
        $this->resetTaxRatesAndFees();
        $storeTaxClasses = $this->getStoreTaxClasses();

        $taxClasses = [];
        if (floatval($invoice->getTaxAmount()) > 0) {
            foreach ($invoice->getAllItems() as $item) {
                $taxClass = $storeTaxClasses[$item->getOrderItem()->getProduct()->getTaxClassId()];
                $taxClassId = $taxClass->getClassId();
                $taxClasses[$taxClassId] = [
                    'title'      => $taxClass->getClassName(),
                    'percent'    => $item->getTaxPercent(),
                    'amount'     => (
                        isset(
                            $taxClasses[$taxClassId]['amount']) ?
                            $taxClasses[$taxClassId]['amount'] :
                            0
                        )
                        + $item->getRowTotal(),
                    'tax_amount' => (
                        isset(
                            $taxClasses[$taxClassId]['tax_amount']) ?
                            $taxClasses[$taxClassId]['tax_amount'] :
                            0
                        )
                        + ($item->getRowTotalInclTax() - $item->getRowTotal()),
                ];
            }
        }

        if (floatval($invoice->getShippingTaxAmount()) > 0) {
            $taxClass = $storeTaxClasses[$this->taxHelper->getShippingTaxClass($invoice->getStoreId())];
            $taxClassId = $taxClass->getClassId();
            $taxClasses[$taxClassId] = [
                'title'      => $taxClass->getClassName(),
                'percent'    => ($invoice->getShippingTaxAmount() / $invoice->getShippingAmount()) * 100,
                'amount'     => (
                    isset(
                        $taxClasses[$taxClassId]['amount']) ?
                        $taxClasses[$taxClassId]['amount'] :
                        0
                    )
                    + $invoice->getShippingAmount(),
                'tax_amount' => (
                    isset(
                        $taxClasses[$taxClassId]['tax_amount']) ?
                        $taxClasses[$taxClassId]['tax_amount'] :
                        0
                    )
                    + $invoice->getShippingTaxAmount(),
            ];
        }

        foreach ($taxClasses as $taxClassId => $taxData) {
            if (FiskalInvoice::TAX_TYPE_PDV == $this->config->getTaxTypeByClassId($taxClassId)) {
                $this->addPdvTaxRate(
                    number_format($taxData['percent'], 2),
                    $prefix * $taxData['amount'],
                    $prefix * $taxData['tax_amount']
                );
            }
        }

        //        @Todo: Trive - commented out intentionally, we chose not to cover these taxes for now
        //        $this->addPdvTaxRate(25, 400, 100, null);
        //        $this->addPdvTaxRate(10.1, 500.1, 15.444, null);
        //        $this->addPnpTaxRate(30.1, 100.1, 10.1, null);
        //        $this->addPnpTaxRate(20.1, 200.1, 20.1, null);
        //        $this->addOtherTaxRate(40.1, 453.3, 12.1, 'Naziv1');
        //        $this->addOtherTaxRate(27.1, 445.1, 50.1, 'Naziv2');
        //        $this->addFee('Naziv1', 0);

        $fiskalInvoice = new FiskalInvoice();
        $fiskalInvoice->setOib($this->config->getOib())
                      ->setRegisteredForPdv($this->config->isRegisteredForPdv())
                      ->setDateTime($this->getCurrentDateTime())
                      ->setInvoiceNumber($invoiceNumber)
                      ->setPdvTaxes($this->getPdvTaxRates())
                      ->setTaxExemptValue(0)
                      ->setMarginTaxValue(0)
                      ->setTaxFreeValue(0)
                      ->setPnpTaxes($this->getPnpTaxRates())
                      ->setOtherTaxes($this->getOtherTaxRates())
                      ->setFees($this->getFees())
                      ->setTotalValue($prefix * $invoice->getGrandTotal())
                      ->setPaymentType(
                          $this->config->getPaymentTypeByCode($invoice->getOrder()->getPayment()->getMethod())
                      )
                      ->setOperatorOib($this->config->getOib());

        $fiskalInvoice->setResendFlag(false);

        return $fiskalInvoice;
    }

    /**
     * Prepare creditmemo for request
     *
     * @param CreditmemoInterface $creditmemo
     * @param string              $invoiceNumber
     *
     * @return FiskalInvoice
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function prepareCreditMemo($creditmemo, $invoiceNumber)
    {
        $prefix = -1;
        $this->resetTaxRatesAndFees();
        $storeTaxClasses = $this->getStoreTaxClasses();

        $taxClasses = [];
        if (floatval($creditmemo->getTaxAmount()) > 0) {
            foreach ($creditmemo->getAllItems() as $item) {
                $taxClass = $storeTaxClasses[$item->getOrderItem()->getProduct()->getTaxClassId()];
                $taxClassId = $taxClass->getClassId();
                $taxClasses[$taxClassId] = [
                    'title'      => $taxClass->getClassName(),
                    'percent'    => $item->getTaxPercent(),
                    'amount'     => (
                        isset(
                            $taxClasses[$taxClassId]['amount']) ?
                            $taxClasses[$taxClassId]['amount'] :
                            0
                        )
                        + $item->getRowTotal(),
                    'tax_amount' => (
                        isset(
                            $taxClasses[$taxClassId]['tax_amount']) ?
                            $taxClasses[$taxClassId]['tax_amount'] :
                            0
                        )
                        + ($item->getRowTotalInclTax() - $item->getRowTotal()),
                ];
            }
        }

        if (floatval($creditmemo->getShippingTaxAmount()) > 0) {
            $taxClass = $storeTaxClasses[$this->taxHelper->getShippingTaxClass($creditmemo->getStoreId())];
            $taxClassId = $taxClass->getClassId();
            $taxClasses[$taxClassId] = [
                'title'      => $taxClass->getClassName(),
                'percent'    => ($creditmemo->getShippingTaxAmount() / $creditmemo->getShippingAmount()) * 100,
                'amount'     => (
                    isset(
                        $taxClasses[$taxClassId]['amount']) ?
                        $taxClasses[$taxClassId]['amount'] :
                        0
                    ) + $creditmemo->getShippingAmount(),
                'tax_amount' => (
                    isset(
                        $taxClasses[$taxClassId]['tax_amount']) ?
                        $taxClasses[$taxClassId]['tax_amount'] :
                        0
                    ) + $creditmemo->getShippingTaxAmount(),
            ];
        }

        foreach ($taxClasses as $taxClassId => $taxData) {
            if (FiskalInvoice::TAX_TYPE_PDV == $this->config->getTaxTypeByClassId($taxClassId)) {
                $this->addPdvTaxRate(
                    number_format($taxData['percent'], 2),
                    $prefix * $taxData['amount'],
                    $prefix * $taxData['tax_amount']
                );
            }
        }

        //        @Todo: Trive - commented out intentionally, we chose not to cover these taxes for now
        //        $this->addPdvTaxRate(25, 400, 100, null);
        //        $this->addPdvTaxRate(10.1, 500.1, 15.444, null);
        //        $this->addPnpTaxRate(30.1, 100.1, 10.1, null);
        //        $this->addPnpTaxRate(20.1, 200.1, 20.1, null);
        //        $this->addOtherTaxRate(40.1, 453.3, 12.1, 'Naziv1');
        //        $this->addOtherTaxRate(27.1, 445.1, 50.1, 'Naziv2');
        //        $this->addFee('Naziv1', 0);

        $fiskalInvoice = new FiskalInvoice();
        $fiskalInvoice->setOib($this->config->getOib())
                      ->setRegisteredForPdv($this->config->isRegisteredForPdv())
                      ->setDateTime($this->getCurrentDateTime())
                      ->setInvoiceNumber($invoiceNumber)
                      ->setPdvTaxes($this->getPdvTaxRates())
                      ->setTaxExemptValue(0)
                      ->setMarginTaxValue(0)
                      ->setTaxFreeValue(0)
                      ->setPnpTaxes($this->getPnpTaxRates())
                      ->setOtherTaxes($this->getOtherTaxRates())
                      ->setFees($this->getFees())
                      ->setTotalValue($prefix * $creditmemo->getGrandTotal())
                      ->setPaymentType(
                          $this->config->getPaymentTypeByCode($creditmemo->getOrder()->getPayment()->getMethod())
                      )
                      ->setOperatorOib($this->config->getOib());

        $fiskalInvoice->setResendFlag(false);

        return $fiskalInvoice;
    }
}