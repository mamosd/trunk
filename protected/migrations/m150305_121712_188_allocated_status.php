<?php

class m150305_121712_188_allocated_status extends CDbMigration
{
	public function up()
	{
            $this->insert ( 'polestar_status', array (
                'Id'    => 'AL',
                'Name'  => 'Allocated',
                'Code'  => 'allocated'
            ));
	}

	public function down()
	{
            $this->execute("delete from polestar_status where Id = 'AL'");
	}
}