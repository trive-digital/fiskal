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

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class PaymentMap extends AbstractFieldArray
{
    /**
     * @var PaymentMethod
     */
    protected $paymentMethodRenderer;

    /**
     * @var PaymentMethod
     */
    protected $fiskalPaymentTypeRenderer;

    /**
     * Retrieve payment method renderer
     *
     * @return PaymentMethod
     */
    protected function getPaymentMethodRenderer()
    {
        if (!$this->paymentMethodRenderer) {
            $this->paymentMethodRenderer = $this->getLayout()->createBlock(
                'Trive\Fiskal\Block\Adminhtml\System\Config\Form\Field\PaymentMethod',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->paymentMethodRenderer->setClass('payment_method_select');
        }

        return $this->paymentMethodRenderer;
    }

    /**
     * Retrieve fiskal payment type renderer
     *
     * @return PaymentMethod
     */
    protected function getFiskalPaymentTypeRenderer()
    {
        if (!$this->fiskalPaymentTypeRenderer) {
            $this->fiskalPaymentTypeRenderer = $this->getLayout()->createBlock(
                'Trive\Fiskal\Block\Adminhtml\System\Config\Form\Field\PaymentType',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->fiskalPaymentTypeRenderer->setClass('fiskal_payment_type_select');
        }

        return $this->fiskalPaymentTypeRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'payment_method_code',
            [
                'label'    => __('Payment Method'),
                'renderer' => $this->getPaymentMethodRenderer()
            ]
        );
        $this->addColumn(
            'fiskal_payment_type',
            [
                'label'    => __('Fiskal Payment'),
                'renderer' => $this->getFiskalPaymentTypeRenderer()
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Payment Map');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     *
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_'.$this->getPaymentMethodRenderer()->calcOptionHash(
            $row->getData('payment_method_code')
        )] = 'selected="selected"';
        $row->setData('option_extra_attrs', $optionExtraAttr);
    }
}
