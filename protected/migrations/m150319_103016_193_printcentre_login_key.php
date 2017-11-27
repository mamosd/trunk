<?php

class m150319_103016_193_printcentre_login_key extends CDbMigration
{
	public function up()
	{
            $this->execute("ALTER TABLE `polestar_printcentre_login`
                            ADD PRIMARY KEY (`PrintCentreId`, `LoginId`);");
	}

	public function down()
	{
            $this->execute("ALTER TABLE `polestar_printcentre_login`
                            DROP PRIMARY KEY;");
	}
}