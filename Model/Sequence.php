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
use Trive\Fiskal\Api\Data\SequenceExtensionInterface;
use Trive\Fiskal\Api\Data\SequenceInterface;

class Sequence extends AbstractExtensibleModel implements SequenceInterface
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventPrefix = 'trive_fiskal_sequence';

    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected $_eventObject = 'sequence';

    protected function _construct()
    {
        $this->_init(ResourceModel\Sequence::class);
    }

    public function getId()
    {
        return $this->_getData(SequenceInterface::ID);
    }

    public function setId($identifier)
    {
        return $this->setData(SequenceInterface::ID, $identifier);
    }

    public function getLocationId()
    {
        return $this->_getData(SequenceInterface::LOCATION_ID);
    }

    public function setLocationId($locationId)
    {
        return $this->setData(SequenceInterface::LOCATION_ID, $locationId);
    }

    public function getYear()
    {
        return $this->_getData(SequenceInterface::YEAR);
    }

    public function setYear($year)
    {
        return $this->setData(SequenceInterface::YEAR, $year);
    }

    public function getIncrement()
    {
        return $this->_getData(SequenceInterface::INCREMENT);
    }

    public function setIncrement($increment)
    {
        return $this->setData(SequenceInterface::INCREMENT, $increment);
    }

    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    public function setExtensionAttributes(SequenceExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
