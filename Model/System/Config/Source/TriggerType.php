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

namespace Trive\Fiskal\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class TriggerType implements ArrayInterface
{
    const DISABLED = 0;

    const CREATION = 1;

    const ORDER_STATUS_CHANGE = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::DISABLED, 'label' => __('Disabled')],
            ['value' => self::CREATION, 'label' => __('Magento Invoice / Creditmemo Creation')],
            ['value' => self::ORDER_STATUS_CHANGE, 'label' => __('Order Status Change')],
        ];
    }
}