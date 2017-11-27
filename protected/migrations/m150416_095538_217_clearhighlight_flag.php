<?php

class m150416_095538_217_clearhighlight_flag extends CDbMigration
{
	public function up()
	{
            $this->AddColumn('polestar_load_history','LoadSpecialInstructions','varchar(250)');
            $this->AddColumn('polestar_job','ClearHighlighting',"varchar(1) default 'N'");
            $this->AddColumn('polestar_job_history','ClearHighlighting',"varchar(1)");
	}

	public function down()
	{
            $this->DropColumn('polestar_load_history','LoadSpecialInstructions');
            $this->DropColumn('polestar_job','ClearHighlighting');
            $this->DropColumn('polestar_job_history','ClearHighlighting');
	}
}