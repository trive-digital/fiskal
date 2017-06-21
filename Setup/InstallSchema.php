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

namespace Trive\Fiskal\Setup;

use \Magento\Framework\Setup\InstallSchemaInterface;
use \Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\Setup\ModuleContextInterface;
use \Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Create table 'trive_fiskal_location'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('trive_fiskal_location')
        )->addColumn(
            'location_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Location ID'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Created At'
        )->addColumn(
            'synced_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Synced At'
        )->addColumn(
            'code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Business location code'
        )->addColumn(
            'payment_device_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Payment device code'
        )->addColumn(
            'street',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Business location street'
        )->addColumn(
            'house_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            4,
            ['nullable' => true],
            'Business location house number'
        )->addColumn(
            'house_number_suffix',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            4,
            ['nullable' => true],
            'Business location house number suffix'
        )->addColumn(
            'zip_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            12,
            ['nullable' => true],
            'Business location ZIP code'
        )->addColumn(
            'settlement',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            35,
            ['nullable' => true],
            'Business location settlement'
        )->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            35,
            ['nullable' => true],
            'Business location city'
        )->addColumn(
            'other_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Other type of business location'
        )->addColumn(
            'business_hours',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Business location business hours'
        )->addColumn(
            'valid_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => false],
            'Business location valid from date'
        )->addColumn(
            'closing_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Business location closing code'
        )->addColumn(
            'specific_purpose',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true],
            'Business location specific purpose'
        )->addColumn(
            'use_full_address',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Use full address for business location'
        )->addIndex(
            $setup->getIdxName(
                'trive_fiskal_location',
                ['code'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['code'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(
                'trive_fiskal_location',
                ['code', 'payment_device_code'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['code', 'payment_device_code'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Fiskal Locations'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'trive_fiskal_cert'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('trive_fiskal_cert')
        )->addColumn(
            'cert_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Cert Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Website Id'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64K',
            [],
            'Content'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Updated At'
        )->addIndex(
            $setup->getIdxName('trive_fiskal_cert', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $setup->getFkName('trive_fiskal_cert', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $setup->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Fiskal Certificate Table'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'trive_fiskal_invoice'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('trive_fiskal_invoice')
        )->addColumn(
            'invoice_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Invoice Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store Id'
        )->addColumn(
            'location_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Location Id'
        )->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Increment Id'
        )->addColumn(
            'invoice_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            62,
            [],
            'Invoice Number'
        )->addColumn(
            'entity_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => false],
            'Entity type code'
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Entity ID'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Created At'
        )->addColumn(
            'jir',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'JIR'
        )->addColumn(
            'zki',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'ZKI'
        )->addColumn(
            'synced_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Synced At'
        )->addColumn(
            'error_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Error Message'
        )->addIndex(
            $setup->getIdxName('trive_fiskal_invoice', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName('trive_fiskal_invoice', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addIndex(
            $setup->getIdxName(
                'trive_fiskal_invoice',
                ['entity_type', 'entity_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['entity_type', 'entity_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Fiskal Invoice Table'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'trive_fiskal_sequence'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('trive_fiskal_sequence')
        )->addColumn(
            'sequence_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Sequence Id'
        )->addColumn(
            'location_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Location Id'
        )->addColumn(
            'year',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            ['nullable' => false],
            'Year'
        )->addColumn(
            'increment',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Increment'
        )->addIndex(
            $setup->getIdxName(
                'trive_fiskal_sequence',
                ['location_id', 'year'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['location_id', 'year'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
