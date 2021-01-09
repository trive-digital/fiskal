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

use Magento\Framework\Model\AbstractExtensibleModel;
use Trive\Fiskal\Api\Data\InvoiceExtensionInterface;
use Trive\Fiskal\Api\Data\InvoiceInterface;

class Invoice extends AbstractExtensibleModel implements InvoiceInterface
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventPrefix = 'trive_fiskal_invoice';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventObject = 'invoice';

    protected function _construct()
    {
        $this->_init(ResourceModel\Invoice::class);
    }

    public function getId()
    {
        return $this->_getData(InvoiceInterface::ID);
    }

    public function setId($identifier)
    {
        return $this->setData(InvoiceInterface::ID, $identifier);
    }

    public function getStoreId()
    {
        return $this->_getData(InvoiceInterface::STORE_ID);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(InvoiceInterface::STORE_ID, $storeId);
    }

    public function getLocationCode()
    {
        return $this->_getData(InvoiceInterface::LOCATION_CODE);
    }

    public function setLocationCode($locationCode)
    {
        return $this->setData(InvoiceInterface::LOCATION_CODE, $locationCode);
    }

    public function getPaymentDeviceCode()
    {
        return $this->_getData(InvoiceInterface::PAYMENT_DEVICE_CODE);
    }

    public function setPaymentDeviceCode($paymentDeviceCode)
    {
        return $this->setData(InvoiceInterface::PAYMENT_DEVICE_CODE, $paymentDeviceCode);
    }

    public function getIncrementId()
    {
        return $this->_getData(InvoiceInterface::INCREMENT_ID);
    }

    public function setIncrementId($incrementId)
    {
        return $this->setData(InvoiceInterface::INCREMENT_ID, $incrementId);
    }

    public function getInvoiceNumber()
    {
        return $this->_getData(InvoiceInterface::INVOICE_NUMBER);
    }

    public function setInvoiceNumber($invoiceNumber)
    {
        return $this->setData(InvoiceInterface::INVOICE_NUMBER, $invoiceNumber);
    }

    public function getEntityType()
    {
        return $this->_getData(InvoiceInterface::ENTITY_TYPE);
    }

    public function setEntityType($entityType)
    {
        return $this->setData(InvoiceInterface::ENTITY_TYPE, $entityType);
    }

    public function getEntityId()
    {
        return $this->_getData(InvoiceInterface::ENTITY_ID);
    }

    public function setEntityId($entityId)
    {
        return $this->setData(InvoiceInterface::ENTITY_ID, $entityId);
    }

    public function getCreatedAt()
    {
        return $this->_getData(InvoiceInterface::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(InvoiceInterface::CREATED_AT, $createdAt);
    }

    public function getSyncedAt()
    {
        return $this->_getData(InvoiceInterface::SYNCED_AT);
    }

    public function setSyncedAt($syncedAt)
    {
        return $this->setData(InvoiceInterface::SYNCED_AT, $syncedAt);
    }

    public function getFiskalDateTime()
    {
        return $this->_getData(InvoiceInterface::FISKAL_DATE_TIME);
    }

    public function setFiskalDateTime($fiskalDateTime)
    {
        return $this->setData(InvoiceInterface::FISKAL_DATE_TIME, $fiskalDateTime);
    }


    public function getJir()
    {
        return $this->_getData(InvoiceInterface::JIR);
    }

    public function setJir($jir)
    {
        return $this->setData(InvoiceInterface::JIR, $jir);
    }

    public function getZki()
    {
        return $this->_getData(InvoiceInterface::ZKI);
    }

    public function setZki($zki)
    {
        return $this->setData(InvoiceInterface::ZKI, $zki);
    }

    public function getErrorMessage()
    {
        return $this->_getData(InvoiceInterface::ERROR_MESSAGE);
    }

    public function setErrorMessage($errorMessage)
    {
        return $this->setData(InvoiceInterface::ERROR_MESSAGE, $errorMessage);
    }

    public function getFiskalDataSent()
    {
        return $this->_getData(InvoiceInterface::FISKAL_DATA_SENT);
    }

    public function setFiskalDataSent($fiskalDataSent)
    {
        return $this->setData(InvoiceInterface::FISKAL_DATA_SENT, $fiskalDataSent);
    }
    
    public function getFiskalDataResponse()
    {
        return $this->_getData(InvoiceInterface::FISKAL_DATA_RESPONSE);
    }

    public function setFiskalDataResponse($fiskalDataResponse)
    {
        return $this->setData(InvoiceInterface::FISKAL_DATA_RESPONSE, $fiskalDataResponse);
    }
    
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(InvoiceExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
