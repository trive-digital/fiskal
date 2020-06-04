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

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class Config extends DataObject
{
    const XML_PATH_ENABLED = 'trive_fiskal/settings/enabled';

    const XML_PATH_OIB = 'trive_fiskal/settings/oib';

    const XML_PATH_REGISTERED_FOR_PDV = 'trive_fiskal/settings/registered_for_pdv';

    const XML_PATH_CERTIFICATE_FILE = 'trive_fiskal/settings/certificate_file';

    const XML_PATH_CERTIFICATE_PASSWORD = 'trive_fiskal/settings/certificate_password';

    const XML_PATH_DEMO = 'trive_fiskal/settings/demo';

    const XML_PATH_DEBUG = 'trive_fiskal/settings/debug';

    const XML_PATH_LOCATION_CODE = 'trive_fiskal/mapping/location_code';

    const XML_PATH_PAYMENT_DEVICE_CODE = 'trive_fiskal/mapping/payment_device_code';

    const XML_PATH_PAYMENT_MAPPING = 'trive_fiskal/mapping/payment_mapping';

    const XML_PATH_TAX_MAPPING = 'trive_fiskal/mapping/tax_mapping';

    const XML_PATH_INVOICE_TRIGGER_TYPE = 'trive_fiskal/triggers/invoice_trigger_type';

    const XML_PATH_INVOICE_TRIGGER_STATUS = 'trive_fiskal/triggers/invoice_trigger_status';

    const XML_PATH_INVOICE_TRIGGER_STATUS_AFTER = 'trive_fiskal/triggers/invoice_trigger_status_after';

    const XML_PATH_CREDITMEMO_TRIGGER_TYPE = 'trive_fiskal/triggers/creditmemo_trigger_type';

    const XML_PATH_CREDITMEMO_TRIGGER_STATUS = 'trive_fiskal/triggers/creditmemo_trigger_status';

    const XML_PATH_CREDITMEMO_TRIGGER_STATUS_AFTER = 'trive_fiskal/triggers/creditmemo_trigger_status_after';

    const XML_PATH_TEMPLATES_AUTO_ADD_TO_INVOICE = 'trive_fiskal/templates/auto_add_to_invoice';

    const XML_PATH_TEMPLATES_AUTO_ADD_TO_CREDITMEMO = 'trive_fiskal/templates/auto_add_to_creditmemo';

    const XML_PATH_TEMPLATES_SEND_INVOICE_EMAIL = 'trive_fiskal/templates/send_invoice_email';

    const XML_PATH_TEMPLATES_SEND_CREDITMEMO_EMAIL = 'trive_fiskal/templates/send_creditmemo_email';


    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Current store id
     *
     * @var int
     */
    protected $storeId;

    /**
     * @var CertFactory
     */
    protected $certFactory;

    /**
     * @param JsonSerializer        $jsonSerializer
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface  $scopeConfig
     * @param CertFactory           $certFactory
     * @param array                 $params
     */
    public function __construct(
        JsonSerializer $jsonSerializer,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        CertFactory $certFactory,
        $params = []
    ) {
        parent::__construct($params);

        $this->jsonSerializer = $jsonSerializer;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->certFactory = $certFactory;
    }

    /**
     * Store ID setter
     *
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = (int)$storeId;

        return $this;
    }

    /**
     * Check if module is enabled
     *
     * @param int $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get OIB form stored config
     *
     * @param int $store
     *
     * @return string
     */
    public function getOib($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_OIB,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if registered for PDV
     *
     * @param int $store
     *
     * @return bool
     */
    public function isRegisteredForPdv($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_REGISTERED_FOR_PDV,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Certificate getter
     *
     * @return string
     */
    public function getCertificate()
    {
        $websiteId = $this->storeManager->getStore($this->storeId)->getWebsiteId();

        return $this->certFactory->create()->loadByWebsite($websiteId, false)->getCertPath();
    }

    /**
     * Get certificate password from stored config
     *
     * @return string
     */
    public function getCertificatePassword()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CERTIFICATE_PASSWORD,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get demo flag from stored config
     *
     * @return int
     */
    public function getDemo()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DEMO,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get demo flag from stored config
     *
     * @return int
     */
    public function getDebug()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DEBUG,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get location code from stored config
     *
     * @return string
     */
    public function getLocationCode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LOCATION_CODE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get payment device code from stored config
     *
     * @return string
     */
    public function getPaymentDeviceCode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_DEVICE_CODE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get payment method mapping from stored config
     *
     * @return array
     */
    public function getPaymentMapping()
    {
        $mapping = $this->jsonSerializer->unserialize(
            $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_MAPPING,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
            )
        );

        return is_array($mapping) ? $mapping : [];
    }

    /**
     * Get payment type by Magento payment method code
     *
     * @param string $paymentMethodCode
     *
     * @return string
     */
    public function getPaymentTypeByCode($paymentMethodCode)
    {
        $mapping = $this->getPaymentMapping();
        $matchingKey = array_keys($mapping)[array_search(
            $paymentMethodCode,
            array_column($mapping, 'payment_method_code'),
            true
        )];

        $matchingValue = null;
        if ($matchingKey) {
            $matchingValue = $mapping[$matchingKey]['fiskal_payment_type'];
        }

        return $matchingValue;
    }

    /**
     * Get tax mapping from stored config
     *
     * @return array
     */
    public function getTaxMapping()
    {
        $mapping = $this->jsonSerializer->unserialize(
            $this->scopeConfig->getValue(
            self::XML_PATH_TAX_MAPPING,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
            )
        );

        return is_array($mapping) ? $mapping : [];
    }

    /**
     * Get payment type by Magento tax class id
     *
     * @param string $taxClassId
     *
     * @return string
     */
    public function getTaxTypeByClassId($taxClassId)
    {
        $mapping = $this->getTaxMapping();
        $matchingKey = array_keys($mapping)[array_search(
            $taxClassId,
            array_column($mapping, 'tax_class_id'),
            true
        )];

        $matchingValue = null;
        if ($matchingKey) {
            $matchingValue = $mapping[$matchingKey]['fiskal_tax_type'];
        }

        return $matchingValue;
    }

    /**
     * Get invoice trigger type from stored config
     *
     * @return int
     */
    public function getInvoiceTriggerType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INVOICE_TRIGGER_TYPE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get invoice status trigger from stored config
     *
     * @return string
     */
    public function getInvoiceTriggerStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_INVOICE_TRIGGER_STATUS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get invoice status after trigger from stored config
     *
     * @return string
     */
    public function getInvoiceTriggerStatusAfter()
    {
        $status = $this->scopeConfig->getValue(
            self::XML_PATH_INVOICE_TRIGGER_STATUS_AFTER,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        return $status ? $status : false;
    }

    /**
     * Get creditmemo trigger type from stored config
     *
     * @return int
     */
    public function getCreditmemoTriggerType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CREDITMEMO_TRIGGER_TYPE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get creditmemo status trigger from stored config
     *
     * @return string
     */
    public function getCreditmemoTriggerStatus()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CREDITMEMO_TRIGGER_STATUS,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get creditmemo status after trigger from stored config
     *
     * @return string
     */
    public function getCreditmemoTriggerStatusAfter()
    {
        $status = $this->scopeConfig->getValue(
            self::XML_PATH_CREDITMEMO_TRIGGER_STATUS_AFTER,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );

        return $status ? $status : false;
    }

    /**
     * Get auto add fiskal data to invoice from stored config
     *
     * @return string
     */
    public function getAutoAddToInvoice()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TEMPLATES_AUTO_ADD_TO_INVOICE,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get auto add fiskal data to invoice from stored config
     *
     * @return string
     */
    public function getSendInvoiceEmail()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TEMPLATES_SEND_INVOICE_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get auto add fiskal data to creditmemo from stored config
     *
     * @return string
     */
    public function getAutoAddToCreditmemo()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TEMPLATES_AUTO_ADD_TO_CREDITMEMO,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * Get auto add fiskal data to invoice from stored config
     *
     * @return string
     */
    public function getSendCreditmemoEmail()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TEMPLATES_SEND_CREDITMEMO_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}