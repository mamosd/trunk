<?php

class m150219_114755_polestar_activity_settings extends CDbMigration
{
	public function up()
	{
            $this->insert('setting', array (
                'Subsystem' => 'polestar',
                'Name'      => 'activity-minutes',
                'Value'     => '30'
            ));
            
            $this->insert('setting', array (
                'Subsystem' => 'polestar',
                'Name'      => 'activity-status',
                'Value'     => 'A;B;LA;NA;SD'
            ));
	}

	public function down()
	{
            $this->execute("delete from setting where Subsystem = 'polestar' AND Name = 'activity-minutes'");
            $this->execute("delete from setting where Subsystem = 'polestar' AND Name = 'activity-status'");
	}
}