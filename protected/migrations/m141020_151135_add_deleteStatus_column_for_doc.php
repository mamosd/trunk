<?php

class m141020_151135_add_deleteStatus_column_for_doc extends CDbMigration
{
	public function up()
	{
            $this->addColumn('finance_contractor_document', 'deleteStatus', 'smallint not null');            
	}

	public function down()
	{
            $this->dropColumn('finance_contractor_document', 'deleteStatus');
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