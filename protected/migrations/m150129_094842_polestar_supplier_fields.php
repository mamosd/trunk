<?php

class m150129_094842_polestar_supplier_fields extends CDbMigration
{
	public function up()
	{
            $this->AddColumn('polestar_supplier_contact','Type','varchar(20)');
            $this->AddColumn('polestar_supplier_contact','ExtensionNo','varchar(20)');
	}

	public function down()
	{
            $this->DropColumn('polestar_supplier_contact','Type');
            $this->DropColumn('polestar_supplier_contact','ExtensionNo');
	}
}