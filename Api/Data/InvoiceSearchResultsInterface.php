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

use Magento\Framework\Api\SearchResultsInterface;

interface InvoiceSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Trive\Fiskal\Api\Data\InvoiceInterface[]
     */
    public function getItems();

    /**
     * @param \Trive\Fiskal\Api\Data\InvoiceInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}