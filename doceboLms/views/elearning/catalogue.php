<div class="top">
	<span class="title">YES Store</span>
</div>

<div class="clear"></div>

<div class="page storePage">
	
	<div class="storeHeader"></div>
	
	<div class="storeRepeat">
		<div class="blockContener">
			<? if(count($products) > 0) { ?>
            	<? foreach($products as $product) { ?>
            	<div class="storeBlock">
            		<div class="blockInfos">
            			<h3><?=$product->title?></h3>
            			<div class="data">
            				<p><?=$product->description?></p>
            				<div class="more">
            					<? if($product->discount_rate > 0) { ?>
            					<a href="index.php?r=elearning/product&idproduct=<?=$product->idProduct?>">
            						<img src="/templates/yes/images/bandeau_monster.png" />
            					</a>
            					<? } ?>
                    			<span class="price"><?=$product->offer_text?></span><br/>
                    			<a href="index.php?r=elearning/product&idproduct=<?=$product->idProduct?>" class="button"><span><span><span>Je m’abonne !</span></span></span></a>
                			</div>
                		</div>
    				</div>
            	</div>
            	<? } ?>
            <? } else { ?>
            <div class="storeBlock">
        		<div class="blockInfos">
        			<h3>Ca suffit !</h3>
        			<div class="data">
        				<p>Vous avez réalisé tous les microlearning de votre catégorie, nous espérons que cela vous a plu et nous vous remercions de nous avoir fait confiance.</p>
        				<br/>
        				<p><i>YES Team</i></p>
            		</div>
				</div>
        	</div>
            <? } ?>
        </div>
    </div>
	<div class="clear"></div>
	<p class="def">* Un microlearning est un exercice de quelques minutes.</p>
</div>