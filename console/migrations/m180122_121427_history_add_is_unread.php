<?php

use yii\db\Migration;

/**
 * Class m180122_121427_history_add_is_unread
 */
class m180122_121427_history_add_is_unread extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('{{%message_history}}', 'is_unread', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('{{%message_history}}', 'is_unread');
    }
}
