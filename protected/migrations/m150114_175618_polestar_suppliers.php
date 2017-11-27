<?php
class m150114_175618_polestar_suppliers extends CDbMigration {

    public function up() {
        $this->createTable ( 'polestar_supplier', array (
            'Id'                => 'pk',
            'Name'              => 'varchar(50) not null',
            'Code'              => 'varchar(50) not null',
            'Mobile'            => 'varchar(20)',
            'Landline'          => 'varchar(20)',
            'Email'             => 'varchar(50)',
            'Live'              => 'int(1) default 1',
            'Address1'          => 'varchar(50)',
            'Address2'          => 'varchar(50)',
            'Address3'          => 'varchar(50)',
            'Address4'          => 'varchar(50)',
            'Postcode'          => 'varchar(50)',
            'BankName'          => 'varchar(20)',
            'BankSortCode'      => 'varchar(7)',
            'BankAccountNumber' => 'varchar(9)',
            'CreatedBy'         => 'int',
            'CreatedDate'       => 'datetime',
            'EditedBy'          => 'int',
            'EditedDate'        => 'datetime',
        ));
        $this->createTable ( 'polestar_supplier_contact', array(
            'Id'            => 'pk',
            'SupplierId'    => 'int(11)',
            'Department'    => 'varchar(50)',
            'Name'          => 'varchar(50)',
            'Surname'       => 'varchar(50)',
            'Telephone'     => 'varchar(50)',
            'Mobile'        => 'varchar(50)',
            'Email'         => 'varchar(50)',
            'CreatedBy'     => 'int',
            'CreatedDate'   => 'datetime',
        ));
        $this->createTable ( 'polestar_supplier_document', array(
            'Id'            => 'pk',
            'SupplierId'    => 'int(11)',
            'FileName'      => 'varchar(512)',
            'UploadedBy'    => 'int',
            'UploadedDate'  => 'datetime',
        ));
    }

    public function down() {
        $this->dropTable ( 'polestar_supplier_document' );
        $this->dropTable ( 'polestar_supplier_contact' );
        $this->dropTable ( 'polestar_supplier' );
    }
}