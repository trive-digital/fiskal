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

namespace Trive\Fiskal\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use Trive\FiskalAPI\Invoice\Invoice;

class PaymentType extends Select
{
    /**
     * Payment types cache
     *
     * @var array
     */
    private $paymentTypes;

    /**
     * Fiskal payment types
     *
     * @var array
     */
    private $fiskalPaymentTypes;

    /**
     * Construct
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->initPaymentTypes();
    }

    /**
     * Init payment types from API
     *
     * @return void
     */
    protected function initPaymentTypes()
    {
        $this->fiskalPaymentTypes = [
            Invoice::PAYMENT_TYPE_CASH          => 'Cash',
            Invoice::PAYMENT_TYPE_CC            => 'Credit Card',
            Invoice::PAYMENT_TYPE_CHECKMO       => 'Check / Money Order',
            Invoice::PAYMENT_TYPE_BANK_TRANSFER => 'Bank Transfer',
            Invoice::PAYMENT_TYPE_OTHER         => 'Other'
        ];
    }

    /**
     * Retrieve allowed payment methods
     *
     * @param int $paymentTypeCode return name by payment type code
     *
     * @return array|string
     */
    protected function getPaymentTypes($paymentTypeCode = null)
    {
        if ($this->paymentTypes === null) {
            $this->paymentTypes = [];
            foreach ($this->fiskalPaymentTypes as $code => $label) {
                $this->paymentTypes[$code] = $label;
            }
        }
        if ($paymentTypeCode !== null) {
            return isset($this->paymentTypes[$paymentTypeCode]) ? $this->paymentTypes[$paymentTypeCode] : null;
        }

        return $this->paymentTypes;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->getPaymentTypes() as $code => $title) {
                $this->addOption($code, addslashes($title));
            }
        }

        return parent::_toHtml();
    }
}
