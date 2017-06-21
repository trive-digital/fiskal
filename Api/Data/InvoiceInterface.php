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

interface InvoiceInterface extends ExtensibleDataInterface
{
    const ID = 'invoice_id';

    const STORE_ID = 'store_id';

    const LOCATION_ID = 'location_id';

    const INCREMENT_ID = 'increment_id';

    const INVOICE_NUMBER = 'invoice_number';

    const ENTITY_TYPE = 'entity_type';

    const ENTITY_ID = 'entity_id';

    const CREATED_AT = 'created_at';

    const SYNCED_AT = 'synced_at';

    const JIR = 'jir';

    const ZKI = 'zki';

    const ERROR_MESSAGE = 'error_message';

    const ENTITY_TYPE_INVOICE = 'invoice';

    const ENTITY_TYPE_CREDITMEMO = 'creditmemo';

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
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getLocationId();

    /**
     * @param int $locationId
     *
     * @return $this
     */
    public function setLocationId($locationId);

    /**
     * @return int
     */
    public function getIncrementId();

    /**
     * @param int $incrementId
     *
     * @return $this
     */
    public function setIncrementId($incrementId);

    /**
     * @return int
     */
    public function getInvoiceNumber();

    /**
     * @param int $invoiceNumber
     *
     * @return $this
     */
    public function setInvoiceNumber($invoiceNumber);

    /**
     * @return string
     */
    public function getEntityType();

    /**
     * @param string $entityType
     *
     * @return $this
     */
    public function setEntityType($entityType);

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
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
    public function getJir();

    /**
     * @param string $jir
     *
     * @return $this
     */
    public function setJir($jir);

    /**
     * @return string
     */
    public function getZki();

    /**
     * @param string $zki
     *
     * @return $this
     */
    public function setZki($zki);

    /**
     * @return string
     */
    public function getErrorMessage();

    /**
     * @param string $errorMessage
     *
     * @return $this
     */
    public function setErrorMessage($errorMessage);

    /**
     * @return \Trive\Fiskal\Api\Data\InvoiceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Trive\Fiskal\Api\Data\InvoiceExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(InvoiceExtensionInterface $extensionAttributes);
}