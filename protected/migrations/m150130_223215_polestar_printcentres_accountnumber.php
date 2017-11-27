<?php
class m150130_223215_polestar_printcentres_accountnumber extends CDbMigration {

    public function up() {
        $this->AddColumn('polestar_printcentre','AccountNumber','varchar(50)');
        $this->AddColumn('polestar_supplier','VatNumber','varchar(50)');
        $this->execute("UPDATE polestar_printcentre SET AccountNumber='91400-POLE-000' WHERE Name='Bicester'");
        $this->execute("UPDATE polestar_printcentre SET AccountNumber='91400-POPL-000' WHERE Name='Leeds'");
        $this->execute("UPDATE polestar_printcentre SET AccountNumber='91400-POLE-000' WHERE Name='Polestar Stones'");
        $this->execute("UPDATE polestar_printcentre SET AccountNumber='91400-POWA-000' WHERE Name='Wakefield'");
        $this->execute("UPDATE polestar_printcentre SET AccountNumber='91400-POSG-000' WHERE Name='Sheffield'");
        $this->execute("UPDATE polestar_printcentre SET AccountNumber='91400-POSW-000' WHERE Name='Web Offset Sheffield'");

        $this->CreateTable('polestar_supplier_printcentre',array(
            'SupplierId' => 'int',
            'PrintcentreId' => 'int',
        ));
    }

    public function down() {
        $this->DropColumn('polestar_printcentre','AccountNumber');
        $this->DropColumn('polestar_supplier','VatNumber','varchar(50)');
        $this->DropTable('polestar_supplier_printcentre');
    }

}