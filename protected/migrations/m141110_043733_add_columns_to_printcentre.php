<?php

class m141110_043733_add_columns_to_printcentre extends CDbMigration
{
	public function up()
	{
            $this->addColumn('printcentre', 'postalCode', 'varchar(20) AFTER Address');
            $this->addColumn('printcentre', 'county', 'varchar(20) AFTER postalCode');
	}

	public function down()
	{
            $this->dropColumn('printcentre', 'postalCode');
            $this->dropColumn('printcentre', 'county');
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