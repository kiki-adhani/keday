<div class="content"> 
    <div id="map" style="width: 100%; height: 530px; color:black;"></div> 
</div> 
<script>
    // Memasukkan Data Titik
    var kedai = new L.LayerGroup();
    var prov = new L.LayerGroup();
    var jlnkd = new L.LayerGroup();
    var kel = new L.LayerGroup();

    // Install Leaflet.js
    var map = L.map('map', { 
        center: [-1.7912604466772375, 116.42311966554416], 
        zoom: 5,
        zoomControl: false,
        layers:[] 
    });

    var GoogleSatelliteHybrid= L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { 
        maxZoom: 22, 
        attribution: 'Latihan Web GIS' 
    }).addTo(map);

    // Menambah Basemap
    var OpenStreetMap_France = L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '&copy; OpenStreetMap France | &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    
    var GoogleMaps = new 
    L.TileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', { opacity: 1.0, 
        attribution: 'Latihan Web GIS' 
    });
    var GoogleRoads = new 
    L.TileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',{ 
        opacity: 1.0, 
        attribution: 'Latihan Web GIS' 
    });

    var baseLayers = {
        'Google Satellite Hybrid': GoogleSatelliteHybrid,
        'OpenStreetMap_France': OpenStreetMap_France, 
        'GoogleMaps': GoogleMaps,
        'GoogleRoads': GoogleRoads,
    };

    // Membuat Data Titik
    var groupedOverlays = {
        "Peta Kedai":{
            'Kedai Kopi' :kedai,
            'Jalan Kedai' :jlnkd,
            'Kelurahan' : kel
        },
        "Peta Dasar": {
            'Ibu Kota Provinsi' :prov
        }
    };

    // Install leaflet-groupedlayercontrol
    // var overlayLayers = {} 
    L.control.groupedLayers(baseLayers, groupedOverlays).addTo(map);
    
    // Install MiniMap
    var osmUrl='https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'; 
    var osmAttrib='Map data &copy; OpenStreetMap contributors';
    var osm2 = new L.TileLayer(osmUrl, {minZoom: 0, maxZoom: 13, attribution: osmAttrib }); 
    var rect1 = {color: "#ff1100", weight: 3};
    var rect2 = {color: "#0000AA", weight: 1, opacity:0, fillOpacity:0}; 
    var miniMap = new L.Control.MiniMap(osm2, {toggleDisplay: true, position : "bottomright", 
    aimingRectOptions : rect1, shadowRectOptions: rect2}).addTo(map);

    // Install leaflet-control-geocoder
    L.Control.geocoder({position :"topleft", collapsed:true}).addTo(map);

    
    // Install Koordinat
    /* GPS enabled geolocation control set to follow the user's location */ 
    var locateControl = L.control.locate({ 
        position: "topleft", 
        drawCircle: true, 
        follow: true, 
        setView: true, 
        keepCurrentZoomLevel: true, 
        markerStyle: { 
            weight: 1, 
            opacity: 0.8, 
            fillOpacity: 0.8 
        }, 
        circleStyle: { 
            weight: 1, 
            clickable: false 
        }, 
        icon: "fa fa-location-arrow", 
        metric: false, 
        strings: { 
            title: "My location", 
            popup: "You are within {distance} {unit} from this point", 
            outsideMapBoundsMsg: "You seem located outside the boundaries of the map" 
        }, 
        locateOptions: { 
            maxZoom: 18, 
            watch: true, 
            enableHighAccuracy: true, 
            maximumAge: 10000, 
            timeout: 10000 
        } 
    }).addTo(map);

    // Install leaflet Control ZoomBar
    var zoom_bar = new L.Control.ZoomBar({position: 'topleft'}).addTo(map);

    // Install Leaflet.Coordinates
    L.control.coordinates({ 
        position:"bottomleft", 
        decimals:2, 
        decimalSeperator:",", 
        labelTemplateLat:"Latitude: {y}", 
        labelTemplateLng:"Longitude: {x}" 
    }).addTo(map);
    /* scala */
    L.control.scale({metric: true, position: "bottomleft"}).addTo(map);

    // Install Mata Angin
    var north = L.control({position: "bottomleft"}); 
    north.onAdd = function(map) { 
        var div = L.DomUtil.create("div", "info legend"); 
        div.innerHTML = '<img src="<?=base_url()?>assets/arah-mata-angin.png"style=width:200px;>'; 
        return div; 
    } 
    north.addTo(map);

    // Menambahkan Titik Kedai
    $.getJSON("<?=base_url()?>assets/kedai.geojson",function(data){ 
        var ratIcon = L.icon({ 
            iconUrl: '<?=base_url()?>assets/Marker-1.png', 
            iconSize: [15,24] 
        });
        L.geoJson(data,{ 
            pointToLayer: function(feature,latlng){ 
                var marker = L.marker(latlng,{icon: ratIcon}); 
                marker.bindPopup(feature.properties.Nama_Coffe); 
                return marker; 
            } 
        }).addTo(kedai);
    });
    
    // Menambahkan Titik Provinsi
    $.getJSON("<?=base_url()?>assets/provinsi.geojson",function(data){ 
        var ratIcon = L.icon({ 
            iconUrl: '<?=base_url()?>assets/Marker-2.png', 
            iconSize: [24,24] 
        });
        L.geoJson(data,{ 
            pointToLayer: function(feature,latlng){ 
                var marker = L.marker(latlng,{icon: ratIcon}); 
                marker.bindPopup(feature.properties.CITY_NAME); 
                return marker; 
            } 
        }).addTo(prov);
    });
    
    // Menambahkan Line Jalan Kedai
    $.getJSON("<?=base_url()?>assets/jalan_kedai.geojson",function(kode){ 
        L.geoJson(kode,{ 
            style: function(feature){
                var color,
                kode = feature.properties.kode;
                if ( kode < 2 ) color = "#f2051d";
                else if ( kode > 0 ) color = "#f2051d";
                else color = "#f2051d"; // no data
                return { color: "#999", weight: 5, color: color, fillOpacity: .8 };
            },
            onEachFeature: function( feature, layer ){
                layer.bindPopup
                ()
            }
        }).addTo(jlnkd);
    });
    
    // Menambahkan Line Jalan Kedai
    $.getJSON("<?=base_url()?>assets/kel_jatisari_jatiluhur.geojson",function(kode){ 
        L.geoJson(kode,{ 
            style: function(feature){
                var fillColor,
                kode = feature.properties.kode;
                if ( kode > 21 ) fillColor = "#006837";
                else if ( kode > 1 ) fillColor = "#c2e699";
                else if ( kode > 0 ) fillColor = "#ffffcc";
                else fillColor = "#f7f7f7"; // no data
                return { color: "#999", weight: 1, fillColor: fillColor, fillOpacity: .6 };
            },
            onEachFeature: function( feature, layer ){
                layer.bindPopup(feature.properties.Kelurahan)
            }
        }).addTo(kel);
    });

    // Membuat Legenda
    const legend = L.control.Legend({
        position: "bottomright",
        title: "Keterangan",
        collapsed: true,
        symbolWidth: 24,
        opacity: 1,
        column: 1,
        legends: [{
            label: "Kedai Kopi",
            type: "image",
            url: "<?=base_url()?>/assets/Marker-1.png",
        },
        {
            label: "Jalan Raya Kedai",
            type: "polyline",
            color: "#f2051d",
            fillColor: "#f2051d",
            weight: 2
        },
        {
            title: "Jalan Raya Kedai"
        },
        {
            label: "Kelurahan Jatisari & Jatiluhur",
            font: 29,
            type: "polygon",
            sides: 4,
            color: "#FF0000",
            fillColor: "#FF0000",
            weight: 2
        }]
    }).addTo(map);
</script>