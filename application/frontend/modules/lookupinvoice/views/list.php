<?php
foreach ($rows as $iv) {
    echo "<tr>";
    ?>
    <td class="text-center"><?= $pos++; ?></td>    
    <td class="text-center">
        <span class="ipdf" itc="<?= $iv['templateCode']; ?>" ino="<?= $iv['invoiceNo']; ?>" iid="<?= $iv['invoiceId']; ?>">pdf</span> | 
        <span class="izip" itc="<?= $iv['templateCode']; ?>" ino="<?= $iv['invoiceNo']; ?>" iid="<?= $iv['invoiceId']; ?>">zip</span>
    </td>
    <td class="text-center">
        <span title="Xem file pdf" class="viewpdf" itc="<?= $iv['templateCode']; ?>" ino="<?= $iv['invoiceNo']; ?>" iid="<?= $iv['invoiceId']; ?>"><i class="fa fa-eye" aria-hidden="true"></i></span>
    </td>
	<td><?= $iv['templateCode']; ?></td>
	<td><?= $iv['invoiceNo']; ?></td>
	<td class="text-center"><?= date('Y-m-d h:i:s', $iv['issueDate'] / 1000); ?></td>
	<td><?= $iv['buyerName']; ?></td>	
    <td><?= number_format($iv['total'],0,",","."); ?></td>
	<td><?= $iv['currency']; ?></td>
	<td>
		<?php
		if($iv['paymentStatus'] == 1) {
			echo "Đã thanh toán";
		} else {
			echo "Chưa thanh toán";
		}
		?>
	</td>
	<!--
	<td>
		<?php
		if($iv['adjustmentType'] == 1) {
			echo "Hóa đơn gốc";
		} else if($iv['adjustmentType'] == 3) {
			echo "Hóa đơn thay thế";
		} else if($iv['adjustmentType'] == 5) {	
			echo "Hóa đơn đã điều chỉnh";
		}
		?>
	</td>
	-->
	
	<!--
    <td><?= $iv['invoiceType']; ?></td>
    <td><?= $iv['adjustmentType']; ?></td>
    <td><?= $iv['templateCode']; ?></td>
    <td><?= $iv['invoiceSeri']; ?></td>
    <td><?= $iv['invoiceNumber']; ?></td>
    <td><?= $iv['invoiceNo']; ?></td>
    <td><?= $iv['currency']; ?></td>
    <td><?= $iv['total']; ?></td>    
    <td><?= date('Y-m-d h:i:s', $iv['issueDate'] / 1000); ?></td>
    <td><?= $iv['state']; ?></td>
    <td><?= $iv['requestDate']; ?></td>
    <td><?= $iv['description']; ?></td>
    <td><?= $iv['buyerIdNo']; ?></td>
    <td><?= $iv['stateCode']; ?></td>
    <td><?= $iv['subscriberNumber']; ?></td>
    <td><?= $iv['paymentStatus']; ?></td>
    <td><?= $iv['viewStatus']; ?></td>
    <td><?= $iv['downloadStatus']; ?></td>
    <td><?= $iv['exchangeStatus']; ?></td>
    <td><?= $iv['numOfExchange']; ?></td>
    <td><?= date('Y-m-d h:i:s', $iv['createTime'] / 1000); ?></td>   
    <td><?= $iv['contractNo']; ?></td>
    <td><?= $iv['supplierTaxCode']; ?></td>
    <td><?= $iv['buyerTaxCode']; ?></td>
    <td><?= $iv['totalBeforeTax']; ?></td>
    <td><?= $iv['taxAmount']; ?></td>
    <td><?= $iv['taxRate']; ?></td>
    <td><?= $iv['paymentMethod']; ?></td>
    <td><?= $iv['paymentTime']; ?></td>   
    <td><?= $iv['buyerName']; ?></td> 
    <td><?= $iv['paymentStatusName']; ?></td>
	-->
    </tr>
    <?php
}