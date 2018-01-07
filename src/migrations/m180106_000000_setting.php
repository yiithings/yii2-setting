<?php

use yii\db\Migration;

/**
 * Class m180106_000000_setting
 */
class m180106_000000_setting extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%setting}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique()->defaultValue(''),
            'group' => $this->string(32)->notNull()->defaultValue(''),
            'type' => $this->string(32)->notNull()->defaultValue('string'),
            'description' => $this->string(255)->null(),
            'value' => $this->text()->null(),
            'default_value' => $this->text()->null(),
            'definition_name' => $this->string(255)->null()->defaultValue(''),
            'definition_data' => $this->binary()->null(),
            'sort_order' => $this->integer(11)->notNull()->defaultValue(50),
            'autoload' => $this->boolean()->null()->defaultValue(0),
            'created_at' => $this->integer(11)->null(),
            'updated_at' => $this->integer(11)->null(),
        ]);

        $this->createIndex('name_index', '{{%setting}}', ['name']);
        $this->createIndex('full_name_unique', '{{%setting}}', ['name', 'group'], true);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%setting}}');
    }
}
