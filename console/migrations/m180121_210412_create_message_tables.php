<?php

use yii\db\Schema;

class m180121_210412_create_message_tables extends yii\db\Migration
{
    public function up()
    {
        $this->createTable('{{%message}}', [
            'id' => $this->primaryKey(),
            'sender_id' => $this->integer()->notNull(),
            'text' => $this->getDb()->getSchema()->createColumnSchemaBuilder('MEDIUMTEXT')->notNull(),
            'created_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createIndex('message-sender', '{{%message}}', 'sender_id', false);


        $this->createTable('{{%message_history}}', [
            'id' => $this->primaryKey(),
            'contact_id' => $this->integer()->notNull(),
            'message_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex('message_history-message', '{{%message_history}}', 'message_id', false);
        $this->createIndex('message_history-contact', '{{%message_history}}', 'contact_id', false);


        $this->addForeignKey('message-sender', '{{%message}}', 'sender_id', '{{%user}}', 'id', 'CASCADE', null);

        $this->addForeignKey('message_history-message', '{{%message_history}}', 'message_id', '{{%message}}', 'id', 'CASCADE', null);
        $this->addForeignKey('message_history-contact', '{{%message_history}}', 'contact_id', '{{%contact}}', 'id', 'CASCADE', null);
    }

    public function down()
    {
        $this->dropForeignKey('message_history-contact', '{{%message_history}}');
        $this->dropForeignKey('message_history-message', '{{%message_history}}');
        $this->dropForeignKey('message-sender', '{{%message}}');

        $this->dropTable('{{%message_history}}');
        $this->dropTable('{{%message}}');
    }
}
