<div class="top">
	<span class="title">YES Store</span>
</div>

<div class="clear"></div>

<div class="page storePage">
	
	<div class="storeHeader"></div>
	
	<div class="storeRepeat">
		<div class="blockContener">
        	<? foreach($products as $product) { ?>
        	<div class="storeBlock">
        		<div class="blockInfos">
        			<h3><?=$product->title?></h3>
        			<div class="data">
        				<p><?=$product->description?></p>
        				<div class="more">
                			<span class="price"><?=$product->offer_text?></span><br/>
                			<span class="little">(soit <?=ProductLms::getTTC($product->amount_ht/$product->abo_months)?> &euro;/mois pour <?=($product->course_count/$product->abo_months)?> microlearning*)</span><br/>
                			<a href="index.php?r=elearning/product&idproduct=<?=$product->idProduct?>" class="button"><span><span><span>Je m’abonne !</span></span></span></a>
            			</div>
            		</div>
				</div>
        	</div>
        	<? } ?>
        </div>
    </div>
	<div class="clear"></div>
	<p class="def">* Un microlearning est un exercice de quelques minutes.</p>
</div>