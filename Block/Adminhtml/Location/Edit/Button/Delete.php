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

namespace Trive\Fiskal\Block\Adminhtml\Location\Edit\Button;

use Trive\Fiskal\Api\LocationRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete implements ButtonProviderInterface
{

    /**
     * Application request.
     *
     * @var RequestInterface
     */
    private $request;

    /**
     * URL builder.
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Fiskal location repository.
     *
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * @param RequestInterface            $request            Application request.
     * @param UrlInterface                $urlBuilder         URL builder.
     * @param LocationRepositoryInterface $locationRepository Fiskal location repository.
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        LocationRepositoryInterface $locationRepository
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->locationRepository = $locationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $locationId = $this->request->getParam('location_id');
        if ($locationId && $this->locationRepository->getById($locationId)) {
            $confirmMessage = __('Are you sure you want to do this?');
            $data = [
                'label'      => __('Delete'),
                'class'      => 'delete',
                'on_click'   => sprintf(
                    "deleteConfirm('%s', '%s')",
                    $confirmMessage,
                    $this->urlBuilder->getUrl('*/*/delete', ['location_id' => $locationId])
                ),
                'sort_order' => 20
            ];
        }

        return $data;
    }
}
