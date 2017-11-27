<?php
/**
 * @property int    Id
 * @property string Name
 * @property string Code
 * @property string Mobile
 * @property string Landline
 * @property string Email
 * @property int    Live
 * @property string BankName
 * @property string BankSortCode
 * @property string BankAccountNumber
 * @property int    CreatedBy
 * @property string CreatedDate
 * @property int    EditedBy
 * @property string EditedDate
*/
class PolestarSupplier extends CActiveRecord
{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'polestar_supplier';
    }

    public function attributeLabels() {
        return array(
            'Name'  => 'Business',
            'Code'  => 'Supplier',
            'Live'      => 'Is Live',
        );
    }

    public function rules()
    {
        return array(
            array('Name','required'),            
            array('BankSortCode, BankAccountNumber', 'numerical', 'integerOnly'=>true),
            array('BankSortCode','length', 'encoding' => 'UTF-8', 'min'=>6, 'max'=>6),
            array('BankAccountNumber','length', 'encoding' => 'UTF-8', 'min'=>8, 'max'=>8),
            array('BankSortCode', 'validateBankAcc', 'className' => 'PolestarSupplier'),
            array('Name', 'validateContactsEmail')
        );
    }

    public function validateContactsEmail($attribute,$params)
    {
        if (isset($_POST['NewContacts']) && !empty($_POST['NewContacts'])) {
            $contacts = $_POST['NewContacts'];
            $validator = new CEmailValidator();
            foreach ($contacts as $key => $c) {
                if (!$validator->validateValue(trim($c['Email'])))
                    $this->addError('', 'Invalid email entered at contact list, please review.');
            }
        }
    }
    
    public function validateBankAcc($attribute,$params)
    {
        if ( !empty( $this->BankSortCode ) && empty( $this->BankAccountNumber ) )
        {
            $this->addError('Bank Sort Code','Bank Account Numer cannot be blank if Bank Sort Code has value.');
        }
        else if ( empty( $this->BankSortCode ) && !empty( $this->BankAccountNumber ) )
        {
            $this->addError('Bank Account Numer (sort code / account number)','Bank Sort Code cannot be blank if Bank Account Numer has value.');
        }
    }

    public function relations() {
        return array(
            'Contacts'  => array( self::HAS_MANY, 'PolestarSupplierContact', 'SupplierId' ),
            'Documents' => array( self::HAS_MANY, 'PolestarSupplierDocument', 'SupplierId' )
        );
    }

    public static function getAllAsOptions() {
        $result = array();

        $ss = PolestarSupplier::model()->findAll("Live = 1");
        foreach($ss as $s) {
            $result[$s->Id] = $s->Name;
        }

        return $result;
    }

}
?>