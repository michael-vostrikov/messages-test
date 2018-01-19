<?php

use yii\db\Migration;

/**
 * Class m180119_095316_change_user_profile
 */
class m180119_095316_change_user_profile extends Migration
{
    private $table = '{{%profile}}';

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn($this->table, 'public_email');
        $this->dropColumn($this->table, 'gravatar_email');
        $this->dropColumn($this->table, 'gravatar_id');
        $this->dropColumn($this->table, 'location');
        $this->dropColumn($this->table, 'website');
        $this->dropColumn($this->table, 'bio');

        $this->addColumn($this->table, 'status', $this->string(4096)->null()->after('name'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m180119_095316_change_user_profile: WARNING: data cannot be restored.\n\n";

        $this->dropColumn($this->table, 'status');

        $this->addColumn($this->table, 'public_email', $this->string(255)->null());
        $this->addColumn($this->table, 'gravatar_email', $this->string(255)->null());
        $this->addColumn($this->table, 'gravatar_id', $this->string(32)->null());
        $this->addColumn($this->table, 'location', $this->string(255)->null());
        $this->addColumn($this->table, 'website', $this->string(255)->null());
        $this->addColumn($this->table, 'bio', $this->text()->null());
    }
}
