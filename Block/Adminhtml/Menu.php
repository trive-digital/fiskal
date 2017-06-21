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

namespace Trive\Fiskal\Block\Adminhtml;

use Magento\Backend\Block\Template;

/**
 * Page Menu
 *
 * @method Menu setTitle(string $title)
 * @method string getTitle()
 */
class Menu extends Template
{
    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Trive_Fiskal::menu.phtml';
}
