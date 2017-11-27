<?php

class m141016_093618_103_delpoint_address extends CDbMigration
{
	public function up()
	{
            $this->execute("ALTER TABLE `deliverypoint`
                CHANGE COLUMN `Address` `Address` VARCHAR(255) NULL DEFAULT NULL AFTER `Name`,
                CHANGE COLUMN `DeliveryComments` `DeliveryComments` VARCHAR(255) NULL DEFAULT NULL AFTER `County`;");
	}

	public function down()
	{
		echo "m141016_093618_103_delpoint_address does not support migration down.\n";
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