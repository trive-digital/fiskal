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

namespace Trive\Fiskal\Observer;

use Magento\Framework\Event\ObserverInterface;
use Trive\Fiskal\Api\Data\InvoiceInterface;
use Trive\Fiskal\Api\Data\InvoiceInterfaceFactory;
use Trive\Fiskal\Api\InvoiceRepositoryInterface;
use Trive\Fiskal\Model\Config;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Framework\DB\Transaction;
use Trive\Fiskal\Model\System\Config\Source\TriggerType;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;

class OrderSaveCommitAfter implements ObserverInterface
{
    /** @var Config */
    protected $config;

    /** @var InvoiceInterfaceFactory */
    protected $invoiceDataFactory;

    /** @var InvoiceRepositoryInterface */
    protected $invoiceRepository;

    /** @var InvoiceService */
    protected $invoiceService;

    /** @var CreditmemoService */
    protected $creditmemoService;

    /** @var CreditmemoFactory */
    protected $creditmemoFactory;

    /** @var Transaction */
    protected $transaction;

    /**
     * Init plugin
     *
     * @param Config                     $config
     * @param InvoiceInterfaceFactory    $invoiceDataFactory
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param InvoiceService             $invoiceService
     * @param CreditmemoService          $creditmemoService
     * @param CreditmemoFactory          $creditmemoFactory
     * @param Transaction                $transaction
     */
    public function __construct(
        Config $config,
        InvoiceInterfaceFactory $invoiceDataFactory,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceService $invoiceService,
        CreditmemoService $creditmemoService,
        CreditmemoFactory $creditmemoFactory,
        Transaction $transaction
    ) {
        $this->config = $config;
        $this->invoiceDataFactory = $invoiceDataFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceService = $invoiceService;
        $this->creditmemoService = $creditmemoService;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->transaction = $transaction;
    }

    /**
     * Run status-based invoice / creditmemo sync
     *
     * @param Observer $observer
     *
     * @return $this
     */

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        $storeId = $order->getStore()->getId();
        $this->config->setStoreId($storeId);

        if (!$this->config->isEnabled($storeId)) {
            return $this;
        }

        $this->autoInvoiceAndAddToQueue($order);
        $this->autoCreditmemoAndAddToQueue($order);

        return $this;
    }

    /**
     * Auto invoice and add to queue
     *
     * @param OrderInterface $order
     *
     * @return $this
     */
    protected function autoInvoiceAndAddToQueue(OrderInterface $order)
    {
        $this->config->setStoreId($order->getStoreId());

        //if invoice trigger type isn't order status change, skip adding invoice
        if ($this->config->getInvoiceTriggerType() != TriggerType::ORDER_STATUS_CHANGE) {
            return $this;
        }

        //if status isn't changed, skip adding invoice
        if ($order->getStatus() == $order->getOrigData('status')) {
            return $this;
        }

        //if order status isn't the invoice trigger status, skip adding invoice
        if ($order->getStatus() != $this->config->getInvoiceTriggerStatus()) {
            return $this;
        }

        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->register();
            $order->setIsInProcess(true);
            $this->transaction->addObject($invoice)->addObject($order)->save();

            $fiskalInvoice = $this->invoiceDataFactory->create();
            $fiskalInvoice->setStoreId($order->getStoreId())
                          ->setLocationCode($this->config->getLocationCode())
                          ->setPaymentDeviceCode($this->config->getPaymentDeviceCode())
                          ->setEntityType(InvoiceInterface::ENTITY_TYPE_INVOICE)
                          ->setEntityId($invoice->getEntityId());
            try {
                $this->invoiceRepository->save($fiskalInvoice);
            } catch (\Exception $e) {
            }
        }

        return $this;
    }

    /**
     * Auto creditmemo and add to queue
     *
     * @param OrderInterface $order
     *
     * @return $this
     */
    protected function autoCreditmemoAndAddToQueue(OrderInterface $order)
    {
        $this->config->setStoreId($order->getStoreId());

        //if invoice trigger type isn't order status change, skip adding invoice
        if ($this->config->getCreditmemoTriggerType() != TriggerType::ORDER_STATUS_CHANGE) {
            return $this;
        }

        //if status isn't changed, skip adding invoice
        if ($order->getStatus() == $order->getOrigData('status')) {
            return $this;
        }

        //if order status isn't the invoice trigger status, skip adding invoice
        if ($order->getStatus() != $this->config->getCreditmemoTriggerStatus()) {
            return $this;
        }

        $creditmemo = $this->creditmemoFactory->createByOrder($order);
        if ($creditmemo->isValidGrandTotal()) {
            $this->creditmemoService->refund($creditmemo);

            $fiskalInvoice = $this->invoiceDataFactory->create();
            $fiskalInvoice->setStoreId($order->getStoreId())
                          ->setLocationCode($this->config->getLocationCode())
                          ->setPaymentDeviceCode($this->config->getPaymentDeviceCode())
                          ->setEntityType(InvoiceInterface::ENTITY_TYPE_CREDITMEMO)
                          ->setEntityId($creditmemo->getEntityId());
            try {
                $this->invoiceRepository->save($fiskalInvoice);
            } catch (\Exception $e) {
            }
        }

        return $this;
    }
}
