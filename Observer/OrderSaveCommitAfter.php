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
use Trive\Fiskal\Model\Config;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Framework\DB\Transaction;
use Trive\Fiskal\Model\FiskalInvoiceService;
use Trive\Fiskal\Model\System\Config\Source\TriggerType;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\Data\OrderInterface;

class OrderSaveCommitAfter implements ObserverInterface
{
    /** @var Config */
    protected $config;

    /** @var InvoiceService */
    protected $invoiceService;

    /** @var CreditmemoService */
    protected $creditmemoService;

    /** @var CreditmemoFactory */
    protected $creditmemoFactory;

    /** @var Transaction */
    protected $transaction;

    /** @var FiskalInvoiceService */
    protected $fiskalInvoiceService;

    /**
     * OrderSaveCommitAfter constructor.
     *
     * @param Config               $config
     * @param InvoiceService       $invoiceService
     * @param CreditmemoService    $creditmemoService
     * @param CreditmemoFactory    $creditmemoFactory
     * @param Transaction          $transaction
     * @param FiskalInvoiceService $fiskalInvoiceService
     */
    public function __construct(
        Config $config,
        InvoiceService $invoiceService,
        CreditmemoService $creditmemoService,
        CreditmemoFactory $creditmemoFactory,
        Transaction $transaction,
        FiskalInvoiceService $fiskalInvoiceService
    ) {
        $this->config = $config;
        $this->invoiceService = $invoiceService;
        $this->creditmemoService = $creditmemoService;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->transaction = $transaction;
        $this->fiskalInvoiceService = $fiskalInvoiceService;
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
            $this->fiskalInvoiceService->createFiskalInvoiceFromInvoice($invoice);
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
            $this->fiskalInvoiceService->createRefundFiskalInvoiceFromCreditmemo($creditmemo);
        }

        return $this;
    }
}
