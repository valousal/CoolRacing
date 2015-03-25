// ---------------------------------------------------------------------------------- //
// ----------------------------------- Leaflet  ------------------------------------- //
// ---------------------------------------------------------------------------------- //

// ------- verif pseudo :
$( document ).ready(function() {

	//------------------------------------------------------
	//--------------------   Map Event ---------------------
	//------------------------------------------------------
	if($('#mapEvent').length){
		var mapEvent = L.map('mapEvent').setView([48.689511, 6.173162], 12);

		//Creation de la mapEvent:
		L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(mapEvent);
	}

	//------------------------------------------------------
	//--------------------   Map Add -----------------------
	//------------------------------------------------------

	if($('#mapAdd').length){
		var mapAdd = L.map('mapAdd').setView([47.407122, 2.707873], 6);
		var Manage_mode = false;
		var i = 1;

		var Tab = new Array();
		var debut = L.marker();
		var fin = L.marker();

		Tab['lat1']= '';
		Tab['lng1']= '';
		Tab['lat2']= '';
		Tab['lng2']= '';

		//Creation de la mapAdd:
		L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
			attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
		}).addTo(mapAdd);
	}

	// ---------  Listener -----------

	$("#switch_manage_mode").live('click', function(){
		changeMode();
	});

	mapAdd.on('click', onMapClick);

	// ---------  Function -----------

	function changeMode(){
		if(Manage_mode == true) {
		    Manage_mode = false;
		    $('#distance').removeAttr("disabled")
		}else{
		    Manage_mode = true;
		    $('#distance').attr("disabled", 'disabled');
		}
	}

	function onMapClick(obj) {
		if(Manage_mode === true){
			if(i === 1) {
			    debut.setLatLng(obj.latlng).addTo(mapAdd);
			    Tab['lat'+i]= obj.latlng.lat;
				Tab['lng'+i]= obj.latlng.lng;
				i++;
				return debut;
			}
			if(i === 2) {
			    fin.setLatLng(obj.latlng).addTo(mapAdd);
			    Tab['lat'+i]= obj.latlng.lat;
				Tab['lng'+i]= obj.latlng.lng;

				var parcours = L.Routing.control({
				    waypoints: [
				        L.latLng(Tab['lat1'], Tab['lng1']),
				        L.latLng(Tab['lat2'], Tab['lng2'])
				    ],
				}).addTo(mapAdd);

				mapAdd.removeLayer(debut);
				mapAdd.removeLayer(fin);

				setDistance();

				i++;
				return
			}
		}
	}

	function setDistance(){
		if(Manage_mode === true){
			$( "#distance" ).val("42");
		}
	} 
		
});





