<?php

class m140927_071006_add_email_to_login extends CDbMigration
{
	public function up()
	{
            $this->addColumn('login', 'Email', 'varchar(50)');
            $this->addColumn('login', 'token', 'varchar(50)');
            $this->createIndex('unique_value', 'login', 'Email', 'True');
	}

	public function down()
	{
            $this->dropColumn('login', 'Email');
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