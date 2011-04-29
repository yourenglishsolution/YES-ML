<?php

	$sess = Docebo::user();
	$sql = "SELECT * FROM ".$GLOBALS['prefix_fw']."_user WHERE idst=".$sess->idst;
	$user = mysql_fetch_object(mysql_query($sql));
	
	$mCommand = new CommandLms();
	$commands = $mCommand->dbSearch(array('user_id' => $user->idst));

?>
<div class="accountContener">
	<h2>
		Mon compte
		<span class="popupClose"><a onclick="">X</a></span>
	</h2>
	
	
	<div class="accountInfos">
		<h3>Mes informations</h3>
		<ul>
			<li><label>Login :</label> <?=str_replace('/', '', $user->userid)?></li>
			<li><label>Nom :</label> <?=$user->lastname?></li>
			<li><label>Prénom :</label> <?=$user->firstname?></li>
			<li><label>Email :</label> <?=$user->email?></li>
		</ul>
	</div>
	
	<? if(count($commands) > 0) { ?>
	<hr/>
	<div class="accountCommands">
		<h3>Mes Abonnements</h3>
		<?
			foreach($commands as $key => $command)
			{
				$products = $mCommand->getProducts($command->command_id);
				$product = array_shift($products);
		?>
		<div class="abo">
			<p>
				<b>#<?=date('d/m/Y', $command->crea)?></b> - <?=$product->title?>
			</p>
			<div>
				<h4>Liste des paiements</h4>
				<table>
					<thead>
						<tr>
							<th>N°</th>
							<th>Date</th>
							<th>Montant TTC</th>
							<th>Facture</th>
						</tr>
					</thead>
					<?
						$payments = $mCommand->getPayments($command->command_id);
						foreach($payments as $key => $payment) {
					?>
					<tr>
						<td><?=($key+1)?></td>
						<td><?=date('d/m/Y \à H:i', $payment->crea)?></td>
						<td><?=ProductLms::priceFormat($payment->amount_ttc)?>&euro;</td>
						<td>
            				<a href="../invoice.php?payment=<?=$payment->payment_id?>">Imprimer votre facture</a>
						</td>
					</tr>
					<? } ?>
				</table>
			</div>
		</div>
		<? } ?>
	</div>
	<? } ?>
</div>