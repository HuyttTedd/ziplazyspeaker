<?php


namespace Mageplaza\LazySpeaker\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class InstallSchema
 * @package Mageplaza\LazySpeaker\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        if (!$installer->tableExists('lazyspeaker_word_entity')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lazyspeaker_word_entity'))
                ->addColumn('id', Table::TYPE_BIGINT, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Word Id')
                ->addColumn('word', Table::TYPE_TEXT, '5M', ['nullable' => false], 'Word')
                ->addColumn('word_type', Table::TYPE_TEXT, 200, ['nullable' => true, 'unsigned' => true], 'Word Type')
                ->addColumn('status', Table::TYPE_INTEGER, 2, [], 'Word Status')
                ->addColumn('word_class', Table::TYPE_TEXT, 200, [], 'Word Class')
                ->addColumn('image', Table::TYPE_TEXT, 200, [], 'Word Image')
                ->addColumn('meaning', Table::TYPE_TEXT, '5M', [], 'Meaning')
                ->addColumn('sentence_example', Table::TYPE_TEXT, '5M', [], 'Sentence Example')
                ->addColumn('sentence_meaning', Table::TYPE_TEXT, '5M', [], 'Sentence Meaning')
                ->addColumn('note', Table::TYPE_TEXT, 100, [], 'Note')
                ->addColumn('youtube_link', Table::TYPE_TEXT, 500, [], 'Youtube Link')
                ->addColumn('word_position', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true], 'Word Position')
                ->addColumn('user_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'User Id')
                ->addForeignKey(
                    $installer->getFkName(
                        'lazyspeaker_word_entity',
                        'user_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'user_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Update At'
                )
                ->setComment('Letters Table');

            $connection->createTable($table);
        }
        if (!$installer->tableExists('lazyspeaker_package_entity')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lazyspeaker_package_entity'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Package Id')
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Package Name')
                ->addColumn('user_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'User Id')
                ->addColumn('package_position', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true], 'Package Position')
                ->addForeignKey(
                    $installer->getFkName(
                        'lazyspeaker_package_entity',
                        'user_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'user_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Update At'
                )
                ->setComment('Word Package Table');

            $connection->createTable($table);
        }

        if (!$installer->tableExists('lazyspeaker_package_word')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lazyspeaker_package_word'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Package Word Id')
                ->addColumn('package_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'Package Id')
                ->addColumn('word_id', Table::TYPE_BIGINT, null, ['nullable' => false, 'unsigned' => true], 'Word Id')
                ->addIndex($installer->getIdxName(
                    'lazyspeaker_package_word',
                    ['package_id', 'word_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE),
                    ['package_id', 'word_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE])
                ->addForeignKey(
                    $installer->getFkName(
                        'lazyspeaker_package_word',
                        'word_id',
                        'lazyspeaker_word_entity',
                        'id'
                    ),
                    'word_id',
                    $installer->getTable('lazyspeaker_word_entity'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'lazyspeaker_package_word',
                        'package_id',
                        'lazyspeaker_package_entity',
                        'id'
                    ),
                    'package_id',
                    $installer->getTable('lazyspeaker_package_entity'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->addColumn('package_word_position', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true], 'Package Word Position')
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Update At'
                )
                ->setComment('Word Package Table');

            $connection->createTable($table);
        }

        if (!$installer->tableExists('lazyspeaker_post_entity')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lazyspeaker_post_entity'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Post Id')
                ->addColumn('title', Table::TYPE_TEXT, 200, ['nullable' => false], 'Post Title')
                ->addColumn('user_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'User Id')
                ->addForeignKey(
                    $installer->getFkName(
                        'lazyspeaker_post_entity',
                        'user_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'user_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Update At'
                )
                ->addColumn('post_position', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true], 'Post Position')
                ->setComment('Post Table');

            $connection->createTable($table);
        }

        if (!$installer->tableExists('lazyspeaker_post_package')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lazyspeaker_post_package'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Post Package Id')
                ->addColumn('package_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'Package Id')
                ->addColumn('post_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'Post Id')
                ->addIndex($installer->getIdxName(
                    'lazyspeaker_post_package',
                    ['package_id', 'post_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE),
                    ['package_id', 'post_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE])
                ->addForeignKey(
                    $installer->getFkName(
                        'lazyspeaker_post_package',
                        'package_id',
                        'lazyspeaker_package_entity',
                        'id'
                    ),
                    'package_id',
                    $installer->getTable('lazyspeaker_package_entity'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'lazyspeaker_post_package',
                        'post_id',
                        'lazyspeaker_post_entity',
                        'id'
                    ),
                    'post_id',
                    $installer->getTable('lazyspeaker_post_entity'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Update At'
                )

                ->setComment('Post Package Table');

            $connection->createTable($table);
        }


        if (!$installer->tableExists('lazyspeaker_like_entity')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lazyspeaker_like_entity'))
                ->addColumn('id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true
                ], 'Like Id')
                ->addColumn('user_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'User Id')
                ->addColumn('post_id', Table::TYPE_INTEGER, null, ['nullable' => false, 'unsigned' => true], 'Post Id')

                ->addForeignKey(
                    $installer->getFkName(
                        'lazyspeaker_like_entity',
                        'post_id',
                        'lazyspeaker_post_entity',
                        'id'
                    ),
                    'post_id',
                    $installer->getTable('lazyspeaker_post_entity'),
                    'id',
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'lazyspeaker_like_entity',
                        'user_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'user_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->setComment('Like Table');

            $connection->createTable($table);
        }


        $installer->endSetup();
    }
}
