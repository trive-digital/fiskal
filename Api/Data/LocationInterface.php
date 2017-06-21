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

namespace Trive\Fiskal\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface LocationInterface extends ExtensibleDataInterface
{
    const ID = 'location_id';

    const CREATED_AT = 'created_at';

    const SYNCED_AT = 'synced_at';

    const CODE = 'code';

    const PAYMENT_DEVICE_CODE = 'payment_device_code';

    const STREET = 'street';

    const HOUSE_NUMBER = 'house_number';

    const HOUSE_NUMBER_SUFFIX = 'house_number_suffix';

    const ZIP_CODE = 'zip_code';

    const SETTLEMENT = 'settlement';

    const CITY = 'city';

    const OTHER_TYPE = 'other_type';

    const BUSINESS_HOURS = 'business_hours';

    const VALID_FROM = 'valid_from';

    const CLOSING_CODE = 'closing_code';

    const SPECIFIC_PURPOSE = 'specific_purpose';

    const USE_FULL_ADDRESS = 'use_full_address';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $identifier
     *
     * @return $this
     */
    public function setId($identifier);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string|null
     */
    public function getSyncedAt();

    /**
     * @param string $syncedAt
     *
     * @return $this
     */
    public function setSyncedAt($syncedAt);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getPaymentDeviceCode();

    /**
     * @param string $paymentDeviceCode
     *
     * @return $this
     */
    public function setPaymentDeviceCode($paymentDeviceCode);

    /**
     * @return string
     */
    public function getStreet();

    /**
     * @param string $street
     *
     * @return $this
     */
    public function setStreet($street);

    /**
     * @return string
     */
    public function getHouseNumber();

    /**
     * @param string $houseNumber
     *
     * @return $this
     */
    public function setHouseNumber($houseNumber);

    /**
     * @return string
     */
    public function getHouseNumberSuffix();

    /**
     * @param string $houseNumberSuffix
     *
     * @return $this
     */
    public function setHouseNumberSuffix($houseNumberSuffix);

    /**
     * @return string
     */
    public function getZipCode();

    /**
     * @param string $zipCode
     *
     * @return $this
     */
    public function setZipCode($zipCode);

    /**
     * @return string
     */
    public function getSettlement();

    /**
     * @param string $settlement
     *
     * @return $this
     */
    public function setSettlement($settlement);

    /**
     * @return string
     */
    public function getCity();

    /**
     * @param string $city
     *
     * @return $this
     */
    public function setCity($city);

    /**
     * @return string
     */
    public function getOtherType();

    /**
     * @param string $otherType
     *
     * @return $this
     */
    public function setOtherType($otherType);

    /**
     * @return string
     */
    public function getBusinessHours();

    /**
     * @param string $businessHours
     *
     * @return $this
     */
    public function setBusinessHours($businessHours);

    /**
     * @return string
     */
    public function getValidFrom();

    /**
     * @param string $validFrom
     *
     * @return $this
     */
    public function setValidFrom($validFrom);

    /**
     * @return string
     */
    public function getClosingCode();

    /**
     * @param string $closingCode
     *
     * @return $this
     */
    public function setClosingCode($closingCode);

    /**
     * @return string
     */
    public function getSpecificPurpose();

    /**
     * @param string $specificPurpose
     *
     * @return $this
     */
    public function setSpecificPurpose($specificPurpose);

    /**
     * @return bool
     */
    public function getUseFullAddress();

    /**
     * @param bool $useFullAddress
     *
     * @return $this
     */
    public function setUseFullAddress($useFullAddress);

    /**
     * @return \Trive\Fiskal\Api\Data\LocationExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Trive\Fiskal\Api\Data\LocationExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(LocationExtensionInterface $extensionAttributes);
}