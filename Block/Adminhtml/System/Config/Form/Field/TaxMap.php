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
class TaxMap extends AbstractFieldArray
{
    /**
     * @var TaxClass
     */
    protected $taxClassRenderer;

    /**
     * @var TaxClass
     */
    protected $fiskalTaxTypeRenderer;

    /**
     * Retrieve tax class renderer
     *
     * @return TaxClass
     */
    protected function getTaxClassRenderer()
    {
        if (!$this->taxClassRenderer) {
            $this->taxClassRenderer = $this->getLayout()->createBlock(
                'Trive\Fiskal\Block\Adminhtml\System\Config\Form\Field\TaxClass',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->taxClassRenderer->setClass('tax_class_select');
        }

        return $this->taxClassRenderer;
    }

    /**
     * Retrieve fiskal tax type renderer
     *
     * @return TaxClass
     */
    protected function getFiskalTaxTypeRenderer()
    {
        if (!$this->fiskalTaxTypeRenderer) {
            $this->fiskalTaxTypeRenderer = $this->getLayout()->createBlock(
                'Trive\Fiskal\Block\Adminhtml\System\Config\Form\Field\TaxType',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->fiskalTaxTypeRenderer->setClass('tax_type_select');
        }

        return $this->fiskalTaxTypeRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('tax_class_id', ['label' => __('Tax Class'), 'renderer' => $this->getTaxClassRenderer()]);
        $this->addColumn(
            'fiskal_tax_type',
            ['label' => __('Fiskal Tax'), 'renderer' => $this->getFiskalTaxTypeRenderer()]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Tax Class Map');
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
        $optionExtraAttr['option_'.$this->getTaxClassRenderer()->calcOptionHash(
            $row->getData('tax_class_id')
        )] = 'selected="selected"';
        $row->setData('option_extra_attrs', $optionExtraAttr);
    }
}
