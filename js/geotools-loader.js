var retrieveGeoToolsCB;

function GeoTools (t_) {
	var pthis = this;
	pthis.token = t_;
	pthis.loaded = false;
	
	
	this.retrieve = function(pc,cb){
		retrieveGeoToolsCB = cb;

		var cs = document.createElement("script");
		
		window.geoToolsCalback = function(data){
			retrieveGeoToolsCB(JSON.parse(data));
			window.geoToolsCalback = null;
			document.body.removeChild(cs);
		};
		
		var token = encodeURIComponent(pthis.token);
		var postcode = encodeURIComponent(pc);
		cs.src = encodeURI("http://geotools.logicc.co.uk/api/postcode_lookup?token="+token+"&postcode="+postcode+"&callback=geoToolsCalback");
		console.log(cs.src);
		cs.type = "text/javascript";
		document.body.appendChild(cs);

	};

}