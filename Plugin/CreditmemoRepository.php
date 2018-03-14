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

use Magento\Sales\Api\CreditmemoRepositoryInterface as Subject;
use Trive\Fiskal\Model\Config;
use Trive\Fiskal\Model\FiskalInvoiceService;
use Trive\Fiskal\Model\System\Config\Source\TriggerType;
use Magento\Sales\Api\Data\CreditmemoInterface;

class CreditmemoRepository
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
     * Run creation-based creditmemo sync
     *
     * @param Subject             $subject
     * @param \Closure            $proceed
     * @param CreditmemoInterface $creditmemo
     *
     * @return CreditmemoInterface
     */
    public function aroundSave(Subject $subject, \Closure $proceed, CreditmemoInterface $creditmemo)
    {
        $result = $proceed($creditmemo);

        $storeId = $creditmemo->getStore()->getId();
        $this->config->setStoreId($storeId);
        if ($this->config->isEnabled($storeId)) {
            $this->creditmemoAddToQueue($result);
        }

        return $result;
    }

    /**
     * Creditmemo add to queue
     *
     * @param CreditmemoInterface $creditmemo
     *
     * @return $this
     */
    protected function creditmemoAddToQueue(CreditmemoInterface $creditmemo)
    {
        $this->config->setStoreId($creditmemo->getStoreId());

        //if invoice trigger type isn't order status change, skip adding invoice
        if ($this->config->getCreditmemoTriggerType() != TriggerType::CREATION) {
            return $this;
        }

        $this->fiskalInvoiceService->createRefundFiskalInvoiceFromCreditmemo($creditmemo);

        return $this;
    }
}
