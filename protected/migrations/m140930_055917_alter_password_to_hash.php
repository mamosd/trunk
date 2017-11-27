<?php

class m140930_055917_alter_password_to_hash extends CDbMigration
{
	public function up()
	{
            $this->update('login', array('Password'=>  new CDbExpression('MD5(Password)')));
	}

	public function down()
	{
		echo "m140930_055917_alter_password_to_hash does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}