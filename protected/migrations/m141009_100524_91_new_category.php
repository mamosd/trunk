<?php

class m141009_100524_91_new_category extends CDbMigration
{
	public function up()
	{
            $this->insert ( "finance_route_category", array (
                "Description" => "Preruns",
                "ContractType" => "DTR",
            ) );
	}

	public function down()
	{
		echo "m141009_100524_91_new_category does not support migration down.\n";
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