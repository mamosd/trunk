<?php
class m150211_154302_162_uploaded_files extends CDbMigration {

    public function up() {
        $this->createTable('polestar_uploaded_file',array(
            'Id' => 'pk',
            'Md5' => 'VARCHAR(32)',
            'FileName' => 'VARCHAR(512)',
            'UploadedBy' => 'INT',
            'UploadedDate' => 'DATETIME',
        ));
        $this->createIndex('polestar_uploaded_file__md5','polestar_uploaded_file','md5');
        $this->createTable('polestar_uploaded_file_job',array(
            'FileId' => 'INT',
            'JobId' => 'INT',
        ));
        $this->createTable('polestar_uploaded_file_load',array(
            'FileId' => 'INT',
            'LoadId' => 'INT',
        ));
    }

    public function down() {
        $this->dropTable('polestar_uploaded_file');
        $this->dropTable('polestar_uploaded_file_job');
        $this->dropTable('polestar_uploaded_file_load');
    }

}