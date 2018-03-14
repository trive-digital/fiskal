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

namespace Trive\Fiskal\Plugin;

use Magento\Sales\Api\Data\InvoiceInterface as Subject;
use Trive\Fiskal\Model\Config;
use Trive\Fiskal\Model\FiskalInvoiceService;
use Trive\Fiskal\Model\System\Config\Source\TriggerType;

class InvoiceInterface
{
    /** @var Config */
    protected $config;

    /** @var FiskalInvoiceService */
    protected $fiskalInvoiceService;

    /**
     * InvoicePayAfter constructor.
     *
     * @param Config               $config
     * @param FiskalInvoiceService $fiskalInvoiceService
     */
    public function __construct(
        Config $config,
        FiskalInvoiceService $fiskalInvoiceService
    ) {
        $this->config = $config;
        $this->fiskalInvoiceService = $fiskalInvoiceService;
    }

    /**
     * Run creation-based invoice sync
     *
     * @param Subject  $subject
     * @param \Closure $proceed
     *
     * @return InvoiceInterface
     */
    public function aroundSave(Subject $subject, \Closure $proceed)
    {
        $result = $proceed($subject);

        $storeId = $subject->getStore()->getId();
        $this->config->setStoreId($storeId);
        if ($this->config->isEnabled($storeId)) {
            $this->invoiceAddToQueue($result);
        }

        return $result;
    }

    /**
     * Invoice add to queue
     *
     * @param Subject $invoice
     *
     * @return $this
     */
    protected function invoiceAddToQueue(Subject $invoice)
    {
        $this->config->setStoreId($invoice->getStoreId());

        //if invoice trigger type isn't order status change, skip adding invoice
        if ($this->config->getInvoiceTriggerType() != TriggerType::CREATION) {
            return $this;
        }

        $this->fiskalInvoiceService->createFiskalInvoiceFromInvoice($invoice);

        return $this;
    }
}
