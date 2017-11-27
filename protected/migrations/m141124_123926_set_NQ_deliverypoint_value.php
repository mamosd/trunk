<?php

class m141124_123926_set_NQ_deliverypoint_value extends CDbMigration
{
	public function up()
	{
            $this->update('deliverypoint', array('NQ'=> 'Primary' ));
	}

	public function down()
	{
		echo "m141124_123926_set_NQ_deliverypoint_value does not support migration down.\n";
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