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

interface SequenceInterface extends ExtensibleDataInterface
{
    const ID = 'sequence_id';

    const LOCATION_ID = 'location_id';

    const YEAR = 'year';

    const INCREMENT = 'increment';

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
    public function getLocationId();

    /**
     * @param string $locationId
     *
     * @return $this
     */
    public function setLocationId($locationId);

    /**
     * @return string|null
     */
    public function getYear();

    /**
     * @param string $year
     *
     * @return $this
     */
    public function setYear($year);

    /**
     * @return string
     */
    public function getIncrement();

    /**
     * @param string $increment
     *
     * @return $this
     */
    public function setIncrement($increment);

    /**
     * @return \Trive\Fiskal\Api\Data\SequenceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * @param \Trive\Fiskal\Api\Data\SequenceExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(SequenceExtensionInterface $extensionAttributes);
}