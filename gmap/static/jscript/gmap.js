function geocodeMarker(address,title,content,category,icon) {
	if (geocoder) {
		geocoder.geocode( { "address" : address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				var latlng = results[0].geometry.location;
				addMarker(results[0].geometry.location,title,content,category,icon)
			}
		});
	}
}

function geocodeCenter(address) {
	if (geocoder) {
		geocoder.geocode( { "address": address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
			} else {
				alert("Geocode was not successful for the following reason: " + status);
			}
		});
	}
}

function addDirection(from,to) {
	var request = {
		origin:from, 
		destination:to,
		travelMode: google.maps.DirectionsTravelMode.DRIVING
	};
	directionsService.route(request, function(response, status) {
		if (status == google.maps.DirectionsStatus.OK) {
			directions.setDirections(response);
		}
	});
	if(infowindow) { 
		infowindow.close(); 
	}
}

function showCategory(category) {
	for (var i=0; i<gmarkers.length; i++) {
		if (gmarkers[i].mycategory == category) {
			gmarkers[i].setVisible(true);
		}
	}
}

function hideCategory(category) {
	for (var i=0; i<gmarkers.length; i++) {
		if (gmarkers[i].mycategory == category) {
			gmarkers[i].setVisible(false);
		}
	}
	if(infowindow) {
		infowindow.close();
	}
}

function hideAll() {
	for (var i=0; i<gmarkers.length; i++) {
		gmarkers[i].setVisible(false);
	}
	if(infowindow) { 
		infowindow.close();
	}
}

function showAll() {
	for (var i=0; i<gmarkers.length; i++) {
		gmarkers[i].setVisible(true);
	}
	if(infowindow) {
		infowindow.close();
	}
}

function toggleHideShow(category) {
	for (var i=0; i<gmarkers.length; i++) {
		if (gmarkers[i].mycategory === category) {
			if (gmarkers[i].getVisible()===true) { gmarkers[i].setVisible(false); }
			else gmarkers[i].setVisible(true);
		}
	}
	if(infowindow) {
		infowindow.close();
	}
}

function addKML(file) {
	var ctaLayer = new google.maps.KmlLayer(file);
	ctaLayer.setMap(map);
}

function getCurrentLat() {
	return current_lat;
}

function getCurrentLng() {
	return current_lng;
}

function callUserData(username, doNotReload)
{
	$.ajaxCall('user.tooltip', 'user_name=' + username, 'GET');
	if(doNotReload)
		return;
	setTimeout(function()
	{
		if($("#js_user_tool_tip_cache_" + username).text() == "")
		{
			callUserData(username, true);
		}
	},1000);
}

function markerZoomTo() {
	value = $("#markerValue").val();
	markerZoomToName(value);
}

function markerZoomToName(value) {
	value = value.toLowerCase();
	workMarker = null;
	
	for (var i = 0; i < gmarkers.length; ++i)
	{
		marker = gmarkers[i];
		if(value.length >= 3 && marker.username.toLowerCase().indexOf(value) != -1)
		{
			workMarker = marker;
		}
	}
	
	if(workMarker == null)
	{
		for (var i = 0; i < gmarkers.length; ++i)
		{
			marker = gmarkers[i];
			if(value.length >= 3 && marker.title.toLowerCase().indexOf(value) != -1)
			{
				workMarker = marker;
			}
		}
	}
	
	if(workMarker != null)
	{
		pt = workMarker.position;
		newpt = new google.maps.LatLng(pt.lat(), pt.lng());
		map.panTo(newpt);

		if (infowindow) {
		infowindow.close();
		}
		
		infowindow = new google.maps.InfoWindow({content: workMarker.content});
		infowindow.setPosition(workMarker.position);
		infowindow.open(map, workMarker);
		callUserData(workMarker.username);
	}
}

function centerToCountry(lat, lng, vnlat, vnlng, vslat, vslng)
{
	var location = new google.maps.LatLng(lat,lng);
	var bounds = new google.maps.LatLngBounds(
		new google.maps.LatLng(vslat, vslng),
		new google.maps.LatLng(vnlat, vnlng)
	);
	
	map.setCenter(location);
	map.fitBounds(bounds);
}

$("#markerValue" ).autocomplete({
    source:function( request, response ) {
        $.ajaxCall('gmap.getUsersAutoComplete', 'startsWith='+request.term)
            .done(function( data ) {
                response( $.parseJSON(data) );
            });
    },
	select: function(event, ui) {
        markerZoomToName(ui.item.value);
		$("#markerValue").val(ui.item.label);
           return false;
    },
    autoFocus: true
});