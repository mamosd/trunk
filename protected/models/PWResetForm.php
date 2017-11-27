<?php

/**
 * PWResetForm class.
 * PWResetForm is the data structure for the password reset form
 * user login form data. It is used by the 'VerToken' action of 'AccountController'.
 */
class PWResetForm extends CFormModel
{
        public $newPassword;
        public $newPassword2;
	public $tokenhid;

        const WEAK = 0;
        const STRONG = 1;        

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
                        array('newPassword', 'required'),
                        array('newPassword2', 'required'),
                        array('newPassword', 'compare', 'compareAttribute'=>'newPassword2'),
                        array('newPassword', 'passwordStrength', 'strength'=>self::STRONG),
		);
	}



        /**
         * check if the user password is strong enough
         * check the password against the pattern requested
         * by the strength parameter
         * This is the 'passwordStrength' validator as declared in rules().
         */
        public function passwordStrength($attribute,$params)
        {
//            if ($params['strength'] === self::WEAK)
//                $pattern = '/^(?=.*[a-zA-Z0-9]).{5,}$/';  
//            elseif ($params['strength'] === self::STRONG)
//                $pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{5,}$/';  
//
//            if(!preg_match($pattern, $this->$attribute))
//              $this->addError($attribute, 'Your password is not strong enough!');
            
            if ( !preg_match('/[A-Z]+[a-z]+[0-9]+/', $this->$attribute) || strlen($this->$attribute)<6  )
            {
                $this->addError($attribute, 'Your password is not strong enough!');
            }
            
        }        
        
        
	/**
	 * Declares attribute labels.
	 */
        public function attributeLabels()
        {
            return array(
                'newPassword'=>'Password',
                'newPassword2'=>'Confirm Password',
            );
        }

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	
}
