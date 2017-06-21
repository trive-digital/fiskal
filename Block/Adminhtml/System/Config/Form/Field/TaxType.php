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

class TaxType extends Select
{
    /**
     * Tax types cache
     *
     * @var array
     */
    private $taxTypes;

    /**
     * Fiskal tax types
     *
     * @var array
     */
    private $fiskalTaxTypes;

    /**
     * TaxType constructor.
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->initTaxTypes();
    }

    /**
     * Init tax types from API
     *
     * @return void
     */
    protected function initTaxTypes()
    {
        $this->fiskalTaxTypes = [
            Invoice::TAX_TYPE_PDV => 'PDV',
            //            @Todo: Trive - commented out intentionally, we chose not to cover these taxes for now
            //            Invoice::TAX_TYPE_PNP => 'PNP',
            //            Invoice::TAX_TYPE_MARGIN => 'Margin Tax',
            //            Invoice::TAX_TYPE_OTHER => 'Other Tax'
        ];
    }

    /**
     * Retrieve allowed tax methods
     *
     * @param int $taxTypeCode return name by tax type code
     *
     * @return array|string
     */
    protected function getTaxTypes($taxTypeCode = null)
    {
        if ($this->taxTypes === null) {
            $this->taxTypes = [];
            foreach ($this->fiskalTaxTypes as $code => $label) {
                $this->taxTypes[$code] = $label;
            }
        }
        if ($taxTypeCode !== null) {
            return isset($this->taxTypes[$taxTypeCode]) ? $this->taxTypes[$taxTypeCode] : null;
        }

        return $this->taxTypes;
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
            foreach ($this->getTaxTypes() as $code => $title) {
                $this->addOption($code, addslashes($title));
            }
        }

        return parent::_toHtml();
    }
}
