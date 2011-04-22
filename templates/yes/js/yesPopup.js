var yesTemp = false;
var yesPopup = new Class({
	
	Implements : [Events, Options],
    searchExp: '',
    mask: null,
    popup: null,
    
    options: {
        contentType: 'ajax',
        ajaxConfig: {
            url: false,
            method: 'get'
            },
        overlay: true,
        popupClass: 'popup',
        maskClass: 'mask',
        closeOnClick:false
    },

	initialize: function(searchExp, options)
    {
        this.searchExp = searchExp;
		this.setOptions(options);
        this.initPopup();
        this.initOverlay();
        this.initElements();
    },
    
    initElements: function()
    {
        var yesPopup = this;
        
        document.getElements(this.searchExp).each(function(item, index)
        {
            item.addEvent('click', function(event)
            {
                event.preventDefault();
                
                switch(yesPopup.options.contentType)
                {
                    case 'text':
                        var text = this.getProperty('rel');
                        yesPopup.showPopup(text);
                        break;
                    
                    case 'ajax': default:
                        var url = yesPopup.options.ajaxConfig.url;
                        if(!url) url = this.getProperty('href');
                        
                        new Request({
                                url: url,
                                method:'get',
                                onSuccess: function(response) { yesPopup.showPopup(response); },
                                onFailure: function() { yesPopup.showPopup("Une erreur est survenue"); }
                            }).send();
                        break;
                }
            });
        }, yesPopup);
    },
    
    // Configuration de la popup
    initPopup: function()
    {
        if(this.popup == null)
        {
            var popup = new Element('div', {
                // La popup possède un ID unique pour ne pas la confondre avec une autre popup
                'id': 'popup'+Number.random(0, 999), 
                'class': this.options.popupClass,
                'styles': { // Styles CSS obligatoires (peuvent être surchargé dans la CSS avec un !important)
                    display:'none',
                    position:'absolute',
                    top:0,
                    left:0,
                    zIndex:1000,
                    overflow:'hidden'
                }
            });
            
            if(this.options.closeOnClick)
            {
                var yesPopup = this;
                popup.addEvent('click', function()
                {
                    yesPopup.hidePopup();
                });
            }
            
            // Configuration de l'effet pour que le dissolve soit identique
            popup.set('reveal', {duration: 100, mode:'vertical'});
            popup.inject(document.body, 'top');
            
            this.popup = popup;
        }
        
        return this.popup;
    },
    
    // Configuration de l'overlay
    initOverlay: function()
    {
        var yesPopup = this;
        this.mask = new Mask({'class':this.options.maskClass, inject:{ where: 'before', target:document.body }});
        
        this.mask.addEvent('click', function()
        {
            yesPopup.hidePopup();
        });
    },
    
    // Affiche la popup et l'overlay
    showPopup: function(content)
    {
        var popup = this.popup;
        
        if(!this.isShow())
        {
            // Positionnement
            popup.position({ relativeTo: document.body, position: 'center' });
            
            // Effet
            /*var fx = new Fx.Reveal(popup, {duration: 100, mode:'vertical'});
            fx.addEvent('show', function() { popup.set('html', content); yesPopup.fireEvent('show'); });
            fx.reveal();*/
            
            popup.setStyle('display', 'block')
            popup.set('html', content);
            
            // Affichage de l'overlay
            this.showOverlay();
            this.fireEvent('show');
        }
        else this.hidePopup();
    },
    
    isShow: function()
    {
        return this.popup.getStyle('display') == 'block';
    },
    
    // Cache la popup et l'overlay
    hidePopup: function()
    {
        this.hideOverlay(); // Enfin, on cache l'overlay
        this.popup.setStyle('display', 'none')
        this.popup.dissolve(); // On cache la popup en premier
        this.popup.set('html', ''); // On vide le contenu pour que le prochain affichage soit correct
    },
    
    // Affiche l'overlay
    showOverlay: function()
    {
		if(this.options.overlay)
        {
            this.mask.show();
		}
	},
    
    // Cache l'overlay
    hideOverlay: function()
    {
		if(this.options.overlay)
        {
            this.mask.hide();
		}
	}
});