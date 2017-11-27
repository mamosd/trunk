<?php
    $this->pageTitle = Yii::app()->name.' - '.Yii::app()->user->role->Description;
    $this->breadcrumbs=array(
                array('label'=>'Home'),
            );
?>

<h1>Welcome to <i><?php echo CHtml::encode(Yii::app()->name); ?></i></h1>

<?php
switch (Yii::app()->user->role->LoginRoleId) {
    case LoginRole::ADMINISTRATOR:
        ?>
        <p>This is a default content for ADMINISTRATORs.</p>
<?php   break;
    case LoginRole::CLIENT:
        ?>
        <p>This is a default content for CLIENTs.</p>
<?php   break;
    case LoginRole::SUPPLIER:
        ?>
        <p>This is a default content for SUPPLIERs.</p>
<?php   break;
    default:
        ?>
        <p>This is a default content for users with no role identified</p>
<?php   break;
}

?>


