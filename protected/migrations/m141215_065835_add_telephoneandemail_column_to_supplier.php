<?php

class m141215_065835_add_telephoneandemail_column_to_supplier extends CDbMigration
{
	public function up()
	{
            $this->addColumn('supplier', 'LandlineNumber', 'varchar(20) AFTER TelephoneNumber');
            $this->addColumn('supplier', 'Email', 'varchar(20) AFTER LandlineNumber');
	}

	public function down()
	{
            $this->dropColumn('supplier', 'LandlineNumber');
            $this->dropColumn('supplier', 'Email');
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