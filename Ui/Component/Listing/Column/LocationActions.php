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

namespace Trive\Fiskal\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class LocationActions extends Column
{
    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item[$this->getData('name')]['edit'] = [
                'href'  => $this->context->getUrl(
                    'trive_fiskal/location/edit',
                    ['location_id' => $item['location_id']]
                ),
                'label' => __('Edit'),
            ];
            $item[$this->getData('name')]['delete'] = [
                'href'    => $this->context->getUrl(
                    'trive_fiskal/location/delete',
                    ['location_id' => $item['location_id']]
                ),
                'label'   => __('Delete'),
                'confirm' => [
                    'title'   => __('Delete'),
                    'message' => __('Are you sure you want to delete location with id: %1?', $item['location_id'])
                ]
            ];
        }

        return $dataSource;
    }
}
