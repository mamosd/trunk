<?php

class m141217_165555_alter_supplier_email_column extends CDbMigration
{
	public function up()
	{
            $this->execute('ALTER TABLE supplier MODIFY Email VARCHAR(50) DEFAULT NULL');
	}

	public function down()
	{
		echo "m141217_165555_alter_supplier_email_column does not support migration down.\n";
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