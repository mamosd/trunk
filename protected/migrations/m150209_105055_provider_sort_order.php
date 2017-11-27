<?php

class m150209_105055_provider_sort_order extends CDbMigration
{
	public function up()
	{
            $this->AddColumn('polestar_supplier', 'ProviderSortOrder', 'int not null default 999');
            $this->AddColumn('polestar_supplier', 'SupplierSortOrder', 'int not null default 999');
            
            $this->execute("UPDATE polestar_supplier SET ProviderSortOrder = 1, SupplierSortOrder = 1 WHERE Id = 1");
	}

	public function down()
	{
            $this->DropColumn('polestar_supplier', 'SupplierSortOrder');
            $this->DropColumn('polestar_supplier', 'ProviderSortOrder');
	}
}