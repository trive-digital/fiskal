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

    const LOCATION_CODE = 'location_code';

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
    public function getLocationCode();

    /**
     * @param string $locationCode
     *
     * @return $this
     */
    public function setLocationCode($locationCode);

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
     * @return int
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