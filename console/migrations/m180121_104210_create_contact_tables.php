<?php

use yii\db\Schema;

class m180121_104210_create_contact_tables extends yii\db\Migration
{
    public function up()
    {
        $this->createTable('{{%contact}}', [
            'id' => $this->primaryKey(),
            'owner_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex('contact-owner', '{{%contact}}', 'owner_id', false);
        $this->createIndex('contact-user', '{{%contact}}', 'user_id', false);


        $this->createTable('{{%contact_request}}', [
            'from_user_id' => $this->integer()->notNull(),
            'to_user_id' => $this->integer()->notNull(),
            'state' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultValue(null),
        ]);
        $this->addPrimaryKey('', '{{%contact_request}}', ['from_user_id', 'to_user_id']);

        $this->createIndex('contact_request-to_user', '{{%contact_request}}', 'to_user_id', false);


        $this->addForeignKey('contact-user', '{{%contact}}', 'user_id', '{{%user}}', 'id', 'CASCADE', null);
        $this->addForeignKey('contact-owner', '{{%contact}}', 'owner_id', '{{%user}}', 'id', 'CASCADE', null);

        $this->addForeignKey('contact_request-to_user', '{{%contact_request}}', 'to_user_id', '{{%user}}', 'id', 'CASCADE', null);
        $this->addForeignKey('contact_request-from_user', '{{%contact_request}}', 'from_user_id', '{{%user}}', 'id', 'CASCADE', null);
    }

    public function down()
    {
        $this->dropForeignKey('contact_request-from_user', '{{%contact_request}}');
        $this->dropForeignKey('contact_request-to_user', '{{%contact_request}}');

        $this->dropForeignKey('contact-owner', '{{%contact}}');
        $this->dropForeignKey('contact-user', '{{%contact}}');

        $this->dropTable('{{%contact_request}}');
        $this->dropTable('{{%contact}}');
    }
}
