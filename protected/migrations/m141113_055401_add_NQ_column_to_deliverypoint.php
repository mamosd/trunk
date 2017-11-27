<?php

class m141113_055401_add_NQ_column_to_deliverypoint extends CDbMigration
{
	public function up()
	{
            $this->addColumn('deliverypoint', 'NQ', 'varchar(20) AFTER DeliveryComments');
	}

	public function down()
	{
            $this->dropColumn('deliverypoint', 'NQ');
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