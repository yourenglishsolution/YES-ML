<script type="text/javascript">

function checkCgv()
{
	var check = document.getElementById('cgv');
	if(check.checked) document.getElementById('paypalForm').submit();
	else alert("Vous devez accepter les CGV avant de continuer");
}

</script>

<div class="top">
	<span class="title">YES Store</span>
</div>

<div class="clear"></div>

<div class="page">
	<div class="product">
		<h2>Récapitulatif de votre commande</h2>
		<p>Pour commander notre produit <b><?=$product->title?></b>, il vous suffit de de cliquez sur le bouton "Je m'abonne !" en dessous du récapitulatif de votre commande. Vous avez choisi l'offre <b><?=$product->title?></b> soit <?=$product->course_count?> microlearning* pour un total de <?=ProductLms::priceFormat(ProductLms::getTTC($product->amount_ht))?> &euro; sur <?=$product->abo_months?> mois.</p>
		<table class="command">
			<tr class="header">
				<td></td>
				<td class="label">Prix TTC</td>
			</tr>
			<tr class="line">
				<td class="label">Abonnement mensuel Microlearning</td>
				<td class="content"><?=ProductLms::priceFormat(ProductLms::getTTC($product->amount_ht)/$product->abo_months)?> &euro;</td>
			</tr>
			<tr class="total">
				<td class="label">Total à payer / mois (durant <?=$product->abo_months?> mois)</td>
				<td class="content"><?=ProductLms::priceFormat(ProductLms::getTTC($product->amount_ht)/$product->abo_months)?> &euro;</td>
			</tr>
			<tr>
				<td class="none">* Exercice de quelques minutes</td>
				<td class="none payment">
				
					<p><input type="checkbox" id="cgv" value="1" /> J'accepte les <a href="index.php?r=elearning/cgv">Conditions Générales de Ventes</a></p>
					
					<form id="paypalForm" action="<?=_PAYPAL_ACCOUNT_URL?>" method="post">
						<input type="hidden" name="cmd" value="_xclick-subscriptions">
						<input type="hidden" name="business" value="<?=_PAYPAL_ACCOUNT_MAIL?>">
						<input name="return" type="hidden" value="http://www.yesmicrolearning.com/return.php" />
						<input name="cancel_return" type="hidden" value="http://www.yesmicrolearning.com/cancel.php" />
						<input name="notify_url" type="hidden" value="http://www.yesmicrolearning.com/paypal.php?op=ok" />
						<input type="hidden" name="lc" value="FR">
						<input type="hidden" name="item_name" value="<?=$product->title?>">
						<input type="hidden" name="item_number" value="1">
						<input type="hidden" name="no_note" value="1">
						<input type="hidden" name="a3" value="<?=(ProductLms::getTTC($product->amount_ht)/$product->abo_months)?>">
						<input type="hidden" name="p3" value="1">
						<input type="hidden" name="t3" value="M">
						<input type="hidden" name="srt" value="<?=$product->abo_months?>">
						<input type="hidden" name="sra" value="1">
						<input type="hidden" name="currency_code" value="EUR">
						<input name="custom" type="hidden" value="<?=$user->idst?>.<?=$product->idProduct?>" />
						<button type="button" class="button" onclick="checkCgv()" value="Je m’abonne !"><span><span><span>Je m’abonne !</span></span></span></button>
					</form>
				</td>
			</tr>
		</table>
		
	</div>
</div>