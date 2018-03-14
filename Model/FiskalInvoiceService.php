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

namespace Trive\Fiskal\Model;

use Trive\Fiskal\Api\Data\InvoiceInterface as FiskalInvoiceInterface;
use Trive\Fiskal\Api\Data\InvoiceInterfaceFactory;
use Trive\Fiskal\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;

class FiskalInvoiceService
{
    /** @var Config */
    protected $config;

    /** @var InvoiceInterfaceFactory */
    protected $invoiceDataFactory;

    /** @var InvoiceRepositoryInterface */
    protected $invoiceRepository;

    /**
     * FiskalInvoiceService constructor.
     *
     * @param Config                     $config
     * @param InvoiceInterfaceFactory    $invoiceDataFactory
     * @param InvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(
        Config $config,
        InvoiceInterfaceFactory $invoiceDataFactory,
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->config = $config;
        $this->invoiceDataFactory = $invoiceDataFactory;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Create fiskal invoice from invoice
     *
     * @param InvoiceInterface $invoice
     */
    public function createFiskalInvoiceFromInvoice($invoice)
    {
        $fiskalInvoice = $this->invoiceDataFactory->create();
        $fiskalInvoice->setStoreId($invoice->getStoreId())
                      ->setLocationCode($this->config->getLocationCode())
                      ->setPaymentDeviceCode($this->config->getPaymentDeviceCode())
                      ->setEntityType(FiskalInvoiceInterface::ENTITY_TYPE_INVOICE)
                      ->setEntityId($invoice->getEntityId());
        try {
            $this->invoiceRepository->save($fiskalInvoice);
        } catch (\Exception $e) {
        }
    }

    /**
     * Create refund fiskal invoice from creditmemo
     *
     * @param CreditmemoInterface $creditmemo
     */
    public function createRefundFiskalInvoiceFromCreditmemo($creditmemo)
    {
        $fiskalInvoice = $this->invoiceDataFactory->create();
        $fiskalInvoice->setStoreId($creditmemo->getStoreId())
                      ->setLocationCode($this->config->getLocationCode())
                      ->setPaymentDeviceCode($this->config->getPaymentDeviceCode())
                      ->setEntityType(FiskalInvoiceInterface::ENTITY_TYPE_CREDITMEMO)
                      ->setEntityId($creditmemo->getEntityId());
        try {
            $this->invoiceRepository->save($fiskalInvoice);
        } catch (\Exception $e) {
        }
    }
}