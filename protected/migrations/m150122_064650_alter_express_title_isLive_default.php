<?php

class m150122_064650_alter_express_title_isLive_default extends CDbMigration
{
	public function up()
	{
            $this->execute('ALTER TABLE express_title MODIFY IsLive smallint(6) not null DEFAULT 0');
	}

	public function down()
	{
		echo "m150122_064650_alter_express_title_isLive_default does not support migration down.\n";
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