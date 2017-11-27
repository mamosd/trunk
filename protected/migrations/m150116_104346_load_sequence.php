<?php

class m150116_104346_load_sequence extends CDbMigration
{
	public function up()
	{
            $this->addColumn('polestar_load', 'Sequence', 'int not null DEFAULT 1 after JobId');
	}

	public function down()
	{
            $this->dropColumn('polestar_load', 'Sequence');
            return false;
	}
}