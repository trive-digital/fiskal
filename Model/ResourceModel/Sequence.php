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

class Sequence extends AbstractDb
{
    /**
     * Sequence constructor.
     *
     * @param Context $context
     * @param null    $resourcePrefix
     */
    public function __construct(
        Context $context,
        $resourcePrefix = null
    ) {
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;

        parent::__construct($context, $resourcePrefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('trive_fiskal_sequence', 'sequence_id');
    }
}
