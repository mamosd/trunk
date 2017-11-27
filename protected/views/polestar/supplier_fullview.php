<style>
    .label {
        font-weight: bold;
    }
    table.listing th {
        text-align: left !important;
    }

.listing.fluid tr:nth-child(even) td {background: none repeat scroll 0 0 #fff;}
.listing.fluid tr:nth-child(odd) td {background: none repeat scroll 0 0 #dfdfdf;}
</style>
<h2><?php echo $model->Name ?></h2>

<fieldset>
    <legend>General Details</legend>

    <table class="listing fluid">
        <tr>
            <th width="15%" style="white-space: nowrap;">Supplier</th>
            <td><?php echo $model->Code ?></td>
        </tr>
        <tr>
            <th width="15%" style="white-space: nowrap;">Name</th>
            <td><?php echo $model->Name ?></td>
        </tr>
        <tr>
            <th width="15%" style="white-space: nowrap;">VAT Number</th>
            <td><?php echo $model->VatNumber ?></td>
        </tr>
        <tr>
            <th width="15%" style="white-space: nowrap;">Is Live</th>
            <td><?php echo $model->Live?'YES':'NO' ?></td>
        </tr>
    </table>
</fieldset>

<fieldset>
    <legend>Address Details</legend>
    <table class="listing fluid">
        <tr>
            <th width="15%" style="white-space: nowrap;">Postcode</th>
            <td><?php echo $model->Postcode ?></td>
        </tr>
        <tr>
            <th width="15%" style="white-space: nowrap;">Address</th>
            <td>
                <?php echo $model->Address1 ?><br/>
                <?php echo $model->Address2 ?><br/>
                <?php echo $model->Address3 ?><br/>
                <?php echo $model->Address4 ?>
            </td>
        </tr>
    </table>
</fieldset>

<fieldset>
    <legend>Account Numbers</legend>
    <?php
    $printCentres = PolestarSupplierPrintCentre::model()->findAll(array(
        'condition' => 'SupplierId = :sid',
        'params' => array(':sid' => $model->Id)
    ));
    foreach($printCentres as $pcm): 
        $pc = PolestarPrintCentre::model()->findByPk($pcm->PrintcentreId);
        ?>
        <?php echo $pc->Name.' ('.$pc->AccountNumber.')' ?><br/>
    <?php endforeach; ?>
</fieldset>

<fieldset>
    <legend>Banking</legend>

    <table class="listing fluid">
        <tr>
            <th width="15%" style="white-space: nowrap;">Bank Name</th>
            <td><?php echo $model->BankName ?></td>
        </tr>
        <tr>
            <th width="15%" style="white-space: nowrap;">Bank Account Numer (sort code / account number)</th>
            <td><?php echo $model->BankSortCode ?> / <?php echo $model->BankAccountNumber ?></td>
        </tr>
    </table>
</fieldset>

<fieldset>
    <legend>Contact Details</legend>

    <table class="listing fluid" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <th>D/N</th>
          <th>Department</th>
          <th>Name</th>
          <th>Landline</th>
          <th>Mobile</th>
          <th>Email</th>
          <th>A/S</th>
        </tr>
                <?php foreach($model->Contacts as $contact): ?>
                <tr>
                    <td><?php echo $contact->Type ?></td>
                    <td><?php echo $contact->Department ?></td>
                    <td><?php echo trim($contact->Name.' '.$contact->Surname) ?></td>
                    <td><?php echo $contact->Telephone.((!empty($contact->ExtensionNo)) ? ' ext '.$contact->ExtensionNo : '') ?></td>
                    <td><?php echo $contact->Mobile ?></td>
                    <td><?php echo $contact->Email ?></td>
                    <td><?php echo $contact->ReceiveAdviceEmails?'YES':'NO' ?></td>
                </tr>
                <?php endforeach; ?>
    </tbody>
    </table>
</fieldset>

<br/>
<button onclick="window.print();">
    Print Supplier Details
</button>

