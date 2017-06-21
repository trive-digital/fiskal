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

namespace Trive\Fiskal\Controller\Adminhtml\Location;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Trive\Fiskal\Api\LocationRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trive_Fiskal::fiskal_location';

    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context                     $context
     * @param LocationRepositoryInterface $locationRepository
     * @param PageFactory                 $resultPageFactory
     */
    public function __construct(
        Context $context,
        LocationRepositoryInterface $locationRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->locationRepository = $locationRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Location edit action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $locationId = (int)$this->getRequest()->getParam('location_id');
        if ($locationId) {
            try {
                $this->locationRepository->getById($locationId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());

                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');

                return $resultRedirect;
            }
        }
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend($locationId ? __('Edit Location') : __('New Location'));

        return $resultPage;
    }
}
