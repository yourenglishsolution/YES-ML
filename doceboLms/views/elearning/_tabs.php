<?/*
	function hex2bin($h)
	{
		if (!is_string($h)) return null;
		$r='';
		for ($a=0; $a<strlen($h); $a+=2) { $r.=chr(hexdec($h{$a}.$h{($a+1)})); }
		return $r;
	}
  
	$sql = 'SELECT * FROM '.$GLOBALS['prefix_lms'].'_scorm_tracking WHERE score_raw>0';
	$packages = mysql_fetch_object(mysql_query($sql));
	var_dump(hex2bin(bin2hex($packages->xmldata)));
*/?>

<div class="top">
	<span class="title">Microlearning <? echo Docebo::user()->getMainGroupLabel()?></span>
	<div>
		<span class="todo">
			<a onclick="changeTab(0)">Exercices à faire</a>
		</span>
		<span class="case" id="caseTodo" onclick="changeTab(0);"><? echo Docebo::user()->getCourseCount(0); ?></span>
		
		<span class="finish">
			<a onclick="changeTab(1);">Exercices terminés</a>
		</span>
		<span class="case" id="caseFinish" onclick="changeTab(1);"><? echo Docebo::user()->getCourseCount(1) ?></span>
	</div>
</div>

<div class="clear"></div>

<div class="page">
	<noscript>
		<div class="block firstBlock">
			<img src="images/content_pict1.png" alt="" />
			<div class="desc">
				<p>La navigation sur ce site nécessite l'activation de javascript.</p>
				<div class="clear"></div>
			</div>
		</div>
	</noscript>
	
	<div id="tab_content">
		<? $this->widget('lms_tab', array('active' => 'elearning')); ?>
	</div>
	
	<script type="text/javascript">
		var intRefresh = null;
		var intInterupt = false;
		var tabView = new YAHOO.widget.TabView();
		
		var newExo = new YAHOO.widget.Tab({
			dataSrc: 'ajax.server.php?r=elearning/inprogress&page=1&rnd=<?php echo time(); ?>',
			cacheData: false
			});
		
		var doneExo = new YAHOO.widget.Tab({
			dataSrc: 'ajax.server.php?r=elearning/completed&page=1&rnd=<?php echo time(); ?>',
			cacheData: false
			});
		
		// Lance la mise à jour des liens après changement du contenu
		newExo.addListener('contentChange', refresh);
		doneExo.addListener('contentChange', refresh);
		
		tabView.addTab(newExo, 0);
		tabView.addTab(doneExo, 1);
		tabView.appendTo('tab_content');
		tabView.getTab(0).addClass('first');
		tabView.set('activeIndex', 0);
		
		// A déclarer avant le chargement complet de la page
		var helpPopup = new yesPopup('.ajaxPopup');
		var waitPopup = new yesPopup('.waitPopup', {
			'ajaxConfig':{ 'url':'index.php?modname=pages&op=wait' },
			'popupClass':'popupWait',
			'closeOnClick':true
			});
		
		// Slide dans les popup des besoins d'aide
		helpPopup.addEvent('show', function()
		{
			var items = [0,1,2,3,4,5,6,7];
			var slide = new noobSlide({
				box: document.id('helpSlide'),
				items: items,
				autoPlay:false,
				size:700,
				addButtons: {
					previous: document.id('leftButton'),
					next: $('rightButton')
				},
				onWalk: function(currentItem)
				{
					// Premier slide affiché, on cache le bouton gauche
					if(currentItem == null) document.id('leftButton').setStyle('display', 'none');
					else document.id('leftButton').setStyle('display', 'block');
					
					// Dernier slide affiché, on cache le bouton droit
					if(currentItem == (items.length - 1)) document.id('rightButton').setStyle('display', 'none');
					else document.id('rightButton').setStyle('display', 'block');
				}
			});
		});
		
		// Après chargement complet de la page
		window.addEvent('domready', function()
		{
			// Activation du refresh en cas d'inactivité de l'utilisateur
			window.addEvent('mousemove', function()
			{
				clearTimeout(intRefresh); 
				if(!intInterupt) intRefresh = setInterval('refreshTabs()', 8000); // Toutes les 8 sec
			});
		});
		
		// Click sur un onglet
		function changeTab(ind)
		{
			newExo.set('dataSrc', 'ajax.server.php?r=elearning/inprogress&page=1&rnd='+Number.random(1, 9999));
			doneExo.set('dataSrc', 'ajax.server.php?r=elearning/completed&page=1&rnd='+Number.random(1, 9999));
			tabView.selectTab(ind);
			
			if(ind == 0)
			{
				$$('.todo a').getLast().setStyle('textDecoration', 'underline');
				$$('.finish a').getLast().setStyle('textDecoration', 'none');
			}
			else
			{
				$$('.todo a').getLast().setStyle('textDecoration', 'none');
				$$('.finish a').getLast().setStyle('textDecoration', 'underline');
			}
		}
		
		// Mise à jour des Event sur les liens des blocks
		function refresh(content)
		{
			// Bloc de gestion de l'erreur de déconnexion
			if(content.newValue.charAt(0) == '{')
			{
				// On affiche un message personnalisé au lieu du message d'erreur
				document.id('tab_content').set('html', "<p>Impossible d'afficher le contenu</p>");
				
				// Test pour ne pas afficher 2 fois le même message
				if(!intInterupt)
				{
					alert("Vous avez été déconnecté, vous allez être redirigé sur la page d'identification.");
					intInterupt = true; // On interrompt la mise à jour du contenu
					window.location = 'index.php'; // On redirige sur le login
				}
				
				return false;
			}
			
			// Gestion des select pour le changement de page
			$$('.selectPage').addEvent('change', function()
			{
				var page = this.getSelected().getProperty('value');
				changePage(page);
			});
			
			// On ré-initilise les popups
			waitPopup.initElements();
			helpPopup.initElements();
		}
		
		// Modification du lien de mise à jour des block et rafraichissement
		function changePage(nbPage)
		{
			newExo.set('dataSrc', 'ajax.server.php?r=elearning/inprogress&page='+nbPage+'&rnd='+Number.random(1, 9999));
			doneExo.set('dataSrc', 'ajax.server.php?r=elearning/completed&page='+nbPage+'&rnd='+Number.random(1, 9999));
			refreshTabs();
		}
		
		// Mise à jour du contenu
		function refreshTabs()
		{
			// Mise à jour des onglets
			newExo.refresh();
			doneExo.refresh();
			
			// Mise à jour du nombre d'exercices
			new Request({
				url: 'index.php?modname=pages&op=nbCourse',
				method:'get',
				onSuccess: function(response)
				{
					var data = JSON.decode(response);
					document.id('caseTodo').set('text', data.todo);
					document.id('caseFinish').set('text', data.done);
				}
			}).send();
		}
	</script>
</div>