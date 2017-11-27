<?php

class m141221_101712_add_isLive_column_to_supplier extends CDbMigration
{
	public function up()
	{
            $this->addColumn('supplier', 'IsLive', 'SMALLINT not null DEFAULT 1');
	}

	public function down()
	{
            $this->dropColumn('supplier', 'IsLive');
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