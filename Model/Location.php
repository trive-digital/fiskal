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
use Trive\Fiskal\Api\Data\LocationExtensionInterface;
use Trive\Fiskal\Api\Data\LocationInterface;

class Location extends AbstractExtensibleModel implements LocationInterface
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventPrefix = 'trive_fiskal_location';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventObject = 'location';

    protected function _construct()
    {
        $this->_init(ResourceModel\Location::class);
    }

    public function getId()
    {
        return $this->_getData(LocationInterface::ID);
    }

    public function setId($identifier)
    {
        return $this->setData(LocationInterface::ID, $identifier);
    }

    public function getCreatedAt()
    {
        return $this->_getData(LocationInterface::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(LocationInterface::CREATED_AT, $createdAt);
    }

    public function getSyncedAt()
    {
        return $this->_getData(LocationInterface::SYNCED_AT);
    }

    public function setSyncedAt($syncedAt)
    {
        return $this->setData(LocationInterface::SYNCED_AT, $syncedAt);
    }

    public function getCode()
    {
        return $this->_getData(LocationInterface::CODE);
    }

    public function setCode($code)
    {
        return $this->setData(LocationInterface::CODE, $code);
    }

    public function getPaymentDeviceCode()
    {
        return $this->_getData(LocationInterface::PAYMENT_DEVICE_CODE);
    }

    public function setPaymentDeviceCode($paymentDeviceCode)
    {
        return $this->setData(LocationInterface::PAYMENT_DEVICE_CODE, $paymentDeviceCode);
    }

    public function getStreet()
    {
        return $this->_getData(LocationInterface::STREET);
    }

    public function setStreet($street)
    {
        return $this->setData(LocationInterface::STREET, $street);
    }

    public function getHouseNumber()
    {
        return $this->_getData(LocationInterface::HOUSE_NUMBER);
    }

    public function setHouseNumber($houseNumber)
    {
        return $this->setData(LocationInterface::HOUSE_NUMBER, $houseNumber);
    }

    public function getHouseNumberSuffix()
    {
        return $this->_getData(LocationInterface::HOUSE_NUMBER_SUFFIX);
    }

    public function setHouseNumberSuffix($houseNumberSuffix)
    {
        return $this->setData(LocationInterface::HOUSE_NUMBER_SUFFIX, $houseNumberSuffix);
    }

    public function getZipCode()
    {
        return $this->_getData(LocationInterface::ZIP_CODE);
    }

    public function setZipCode($zipCode)
    {
        return $this->setData(LocationInterface::ZIP_CODE, $zipCode);
    }

    public function getSettlement()
    {
        return $this->_getData(LocationInterface::SETTLEMENT);
    }

    public function setSettlement($settlement)
    {
        return $this->setData(LocationInterface::SETTLEMENT, $settlement);
    }

    public function getCity()
    {
        return $this->_getData(LocationInterface::CITY);
    }

    public function setCity($city)
    {
        return $this->setData(LocationInterface::CITY, $city);
    }

    public function getOtherType()
    {
        return $this->_getData(LocationInterface::OTHER_TYPE);
    }

    public function setOtherType($otherType)
    {
        return $this->setData(LocationInterface::OTHER_TYPE, $otherType);
    }

    public function getBusinessHours()
    {
        return $this->_getData(LocationInterface::BUSINESS_HOURS);
    }

    public function setBusinessHours($businessHours)
    {
        return $this->setData(LocationInterface::BUSINESS_HOURS, $businessHours);
    }

    public function getValidFrom()
    {
        return $this->_getData(LocationInterface::VALID_FROM);
    }

    public function setValidFrom($validFrom)
    {
        return $this->setData(LocationInterface::VALID_FROM, $validFrom);
    }

    public function getClosingCode()
    {
        return $this->_getData(LocationInterface::CLOSING_CODE);
    }

    public function setClosingCode($closingCode)
    {
        return $this->setData(LocationInterface::CLOSING_CODE, $closingCode);
    }

    public function getSpecificPurpose()
    {
        return $this->_getData(LocationInterface::SPECIFIC_PURPOSE);
    }

    public function setSpecificPurpose($specificPurpose)
    {
        return $this->setData(LocationInterface::SPECIFIC_PURPOSE, $specificPurpose);
    }

    public function getUseFullAddress()
    {
        return $this->_getData(LocationInterface::USE_FULL_ADDRESS);
    }

    public function setUseFullAddress($fullAddress)
    {
        return $this->setData(LocationInterface::USE_FULL_ADDRESS, $fullAddress);
    }

    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(LocationExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
