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

        $this->createCertTable($setup);
        $this->createInvoiceTable($setup);
        $this->createSequenceTable($setup);
    }

    /**
     * Create table 'trive_fiskal_cert'
     *
     * @param SchemaSetupInterface $setup
     */
    private function createCertTable($setup)
    {
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
    }

    /**
     * Create table 'trive_fiskal_invoice'
     *
     * @param SchemaSetupInterface $setup
     */
    private function createInvoiceTable($setup)
    {
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
            'location_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Location code'
        )->addColumn(
            'payment_device_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Payment device code'
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
    }

    /**
     * Create table 'trive_fiskal_sequence'
     *
     * @param SchemaSetupInterface $setup
     */
    private function createSequenceTable($setup)
    {

        $table = $setup->getConnection()->newTable(
            $setup->getTable('trive_fiskal_sequence')
        )->addColumn(
            'sequence_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Sequence Id'
        )->addColumn(
            'location_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            ['nullable' => false],
            'Location code'
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
                ['location_code', 'year'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['location_code', 'year'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
