<?php

class m141217_060712_alter_deliverypoint_time_null_column extends CDbMigration
{
	public function up()
	{
            $this->execute('ALTER TABLE deliverypoint_time MODIFY StartTime VARCHAR(10) DEFAULT NULL');
            $this->execute('ALTER TABLE deliverypoint_time MODIFY EndTime VARCHAR(10) DEFAULT NULL');
	}

	public function down()
	{
		echo "m141217_060712_alter_deliverypoint_time_null_column does not support migration down.\n";
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