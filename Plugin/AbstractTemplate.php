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

use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;
use Magento\Sales\Model\Order\Email\Container\CreditmemoIdentity;
use Magento\Email\Model\AbstractTemplate as AbstractTemplateSubject;
use Trive\Fiskal\Api\Data\InvoiceInterface;
use Trive\Fiskal\Model\Config;
use Magento\Store\Model\StoreManagerInterface;

class AbstractTemplate
{
    /**
     * @var InvoiceIdentity
     */
    protected $invoiceIdentity;

    /**
     * @var CreditmemoIdentity
     */
    protected $creditmemoIdentity;

    /**
     * Store templates cache
     *
     * @var array
     */
    protected $storeTemplates;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Store config
     *
     * @var Config
     */
    protected $config;

    /**
     * AbstractTemplate constructor.
     *
     * @param InvoiceIdentity       $invoiceIdentity
     * @param CreditmemoIdentity    $creditmemoIdentity
     * @param StoreManagerInterface $storeManager
     * @param Config                $config
     */
    public function __construct(
        InvoiceIdentity $invoiceIdentity,
        CreditmemoIdentity $creditmemoIdentity,
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->invoiceIdentity = $invoiceIdentity;
        $this->creditmemoIdentity = $creditmemoIdentity;
        $this->config = $config;
        $this->storeManager = $storeManager;

        $this->storeTemplates = [
            $this->invoiceIdentity->getTemplateId()         => InvoiceInterface::ENTITY_TYPE_INVOICE,
            $this->invoiceIdentity->getGuestTemplateId()    => InvoiceInterface::ENTITY_TYPE_INVOICE,
            $this->creditmemoIdentity->getTemplateId()      => InvoiceInterface::ENTITY_TYPE_CREDITMEMO,
            $this->creditmemoIdentity->getGuestTemplateId() => InvoiceInterface::ENTITY_TYPE_CREDITMEMO
        ];
    }

    /**
     * Append fiskal text to bottom of invoice/creditmemo email
     *
     * @param AbstractTemplateSubject $subject
     * @param                         $variables
     *
     * @return $this|array
     */
    public function beforeGetProcessedTemplate(
        AbstractTemplateSubject $subject,
        $variables
    ) {
        $storeId = $this->storeManager->getStore()->getId();
        $this->config->setStoreId($storeId);
        if (!$this->config->isEnabled($storeId)) {
            return $this;
        }

        if (in_array($subject->getTemplateId(), array_keys($this->storeTemplates))) {
            $type = $this->storeTemplates[$subject->getTemplateId()];
            if (($type == InvoiceInterface::ENTITY_TYPE_INVOICE && $this->config->getAutoAddToInvoice())
                ||
                ($type == InvoiceInterface::ENTITY_TYPE_CREDITMEMO && $this->config->getAutoAddToCreditmemo())
            ) {
                $text = $subject->getTemplateText();
                $text .= '<table style="text-align: center;" width="100%" align="center"><tbody>';
                $text .= '<tr><td>{{trans "Broj računa: %fiskal_invoice_number" fiskal_invoice_number=$'.$type.'.fiskal_invoice_number}}</td></tr>';
                $text .= '<tr><td>{{trans "Vrijeme izdavanja računa: %fiskal_synced_at" fiskal_synced_at=$'.$type.'.fiskal_synced_at}}</td></tr>';
                $text .= '<tr><td>{{trans "JIR: %fiskal_jir" fiskal_jir=$'.$type.'.fiskal_jir}}</td></tr>';
                $text .= '<tr><td>{{trans "Zaštitni kod: %fiskal_zki" fiskal_zki=$'.$type.'.fiskal_zki}}</td></tr>';
                $text .= '</tbody></table>';
                $subject->setTemplateText($text);
            }
        }

        return [$variables];
    }
}
