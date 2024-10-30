jQuery(document).ready(function(){
	var platform = new H.service.Platform(here_api);
	
	var mapDefaulttypes = platform.createDefaultLayers();

	var listitems = document.getElementsByClassName('mona-here-map');

	for (var i = 0; i < listitems.length; i++){
		if(listitems[i].classList.contains('mona-here-map-address')){
			getHereMapAddress(listitems[i]);
		}else{
			var lat = parseFloat(listitems[i].getAttribute('data-lat')) || 0;
			var lng = parseFloat(listitems[i].getAttribute('data-lng')) || 0;
			var place = {lat: lat, lng: lng};
			
			getHereMap(listitems[i], place);
		}
	}
    
    function getHereMapAddress(mapDiv){
		var address = mapDiv.getAttribute('data-address');	
		
		var geocoder = platform.getGeocodingService(),
		geocodingParameters = {
			searchText: address,
			jsonattributes : 1
		};

		geocoder.geocode(
			geocodingParameters,
			function(result){
				var location = result.response.view[0].result;
				
				if(location.length > 0){
					location = location[0];
					
					var place = {
						lat: location.location.displayPosition.latitude, 
						lng: location.location.displayPosition.longitude
					};
					
					getHereMap(mapDiv, place);
				}
			},
			function(error) {
				console.log(error);
			}
		);
	}
    
    function getHereMap(mapDiv, place){
		var zoom = parseInt(mapDiv.getAttribute('data-zoom')) || 15;
		var text = mapDiv.getAttribute('data-text');
		var imageIcon = mapDiv.getAttribute('data-icon');
		var draggable = mapDiv.getAttribute('data-draggable') || false;
		
		// Map
		var map = new H.Map(
			mapDiv,
			mapDefaulttypes.normal.map,
			{
				zoom: zoom,
				center: place
			}
		);		
		
		// UI
		var ui = H.ui.UI.createDefault(map, mapDefaulttypes, 'en-US');
		
		// Marker    
        if(imageIcon == ''){                        
            var marker = new H.map.Marker(place);
        }else{
            var icon = new H.map.Icon(imageIcon);            
            var marker = new H.map.Marker(place, {icon: icon});
        }
		
		// Events
        if(draggable == 1){
            var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
            
            addDraggableMarker(map, behavior);
        }
        
        // Bubble
        if(text != ''){
            addInfoBubble(map, ui, marker, text);
        }else{            
            map.addObject(marker);
        }
    }
    
    function addDraggableMarker(map, behavior){
        map.addEventListener('dragstart', function(ev) {
            var target = ev.target;
            if (target instanceof H.map.Marker) {
                behavior.disable();
            }
        }, false);
        
        map.addEventListener('dragend', function(ev) {
            var target = ev.target;
            if (target instanceof mapsjs.map.Marker) {
                behavior.enable();
            }
        }, false);
        
        map.addEventListener('drag', function(ev) {
            var target = ev.target,
            pointer = ev.currentPointer;
            
            if (target instanceof mapsjs.map.Marker) {
                target.setPosition(map.screenToGeo(pointer.viewportX, pointer.viewportY));
            }
        }, false);
    }
    
    function addInfoBubble(map, ui, marker, text){
        var group = new H.map.Group();
        
        group.addEventListener('tap', function (evt) { console.log(evt.target.getPosition());
            var bubble =  new H.ui.InfoBubble(evt.target.getPosition(), {          
                content: evt.target.getData()
            });
            
            ui.addBubble(bubble);
        }, false);
        
        marker.setData('<div>'+text+'</div>');
        group.addObject(marker);
        
        map.addObject(group);
    }
});