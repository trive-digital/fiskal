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

namespace Trive\Fiskal\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTimeDateTime;
use Magento\Framework\Model\AbstractModel;
use Trive\Fiskal\Model\Cert as CertModel;

class Cert extends AbstractDb
{
    /**
     * @var DateTimeDateTime
     */
    protected $coreDate;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @param Context          $context
     * @param DateTime         $dateTime
     * @param DateTimeDateTime $coreDate
     * @param string           $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        DateTimeDateTime $coreDate,
        $connectionName = null
    ) {
        $this->coreDate = $coreDate;
        $this->dateTime = $dateTime;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize connection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('trive_fiskal_cert', 'cert_id');
    }

    /**
     * Set date of last update
     *
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->formatDate($this->coreDate->gmtDate()));

        return parent::_beforeSave($object);
    }

    /**
     * Load model by website id
     *
     * @param CertModel $object
     * @param bool      $strictLoad
     *
     * @return CertModel
     */
    public function loadByWebsite($object, $strictLoad = true)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(['main_table' => $this->getMainTable()]);

        if ($strictLoad) {
            $select->where('main_table.website_id =?', $object->getWebsiteId());
        } else {
            $select->where(
                'main_table.website_id IN(0, ?)',
                $object->getWebsiteId()
            )->order(
                'main_table.website_id DESC'
            )->limit(
                1
            );
        }

        $data = $connection->fetchRow($select);
        if ($data) {
            $object->setData($data);
        }

        return $object;
    }
}
