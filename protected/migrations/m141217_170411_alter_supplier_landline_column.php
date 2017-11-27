<?php

class m141217_170411_alter_supplier_landline_column extends CDbMigration
{
	public function up()
	{
            $this->execute('ALTER TABLE supplier MODIFY LandlineNumber VARCHAR(45) DEFAULT NULL');
	}

	public function down()
	{
		echo "m141217_170411_alter_supplier_landline_column does not support migration down.\n";
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