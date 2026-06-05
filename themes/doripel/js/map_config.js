/*
Verifica se o host é localhost, caso seja retorna a baseUrl
* com o acréscimo do nome da pasta no servidor local
*Se for um host online, apenas retora o seu domínio.
*@returns {String} Retorna a base url do site.
*/
function getBaseUrl() {
    // Nome do host
    var hostName = location.hostname;
    if (hostName === "localhost") {
        // Endereço após o domínio do site
        pathname = window.location.pathname;
        // Separa o pathname com uma barra transformando o resultado em um array
        splitPath = pathname.split('/');

        // Obtém o segundo valor do array, que é o nome da pasta do servidor local
        path = splitPath[1];

        baseUrl = "https://" + hostName + "/" + path;
    } else {
        baseUrl = "https://" + hostName;
    }
    return baseUrl;
}

var map;

//OVERLAY MAP
class OverlayMap {
    constructor(tileSize) {
        this.tileSize = tileSize;
    }

    getTile(coord, zoom, ownerDocument) {
        var div = ownerDocument.createElement('div');
        div.style.width = this.tileSize.width + 'px';
        div.style.height = this.tileSize.height + 'px';
        div.style.fontSize = '10px';
        div.style.borderStyle = 'solid';
        div.style.borderWidth = '1px';
        div.style.borderColor = '#ccc';
        return div;
    }
}


//FUNÇÃO PRINCIPAL DE CARREGAMENTO DO MAPA E OPTIONS
function initMap() {

    //Passa coordenadas (latitude, longitude) para função carregar o mapa
    var latlng = "<?= $device_location; ?>"

    var arr = latlng.split(',');
    var myLatlng = new google.maps.LatLng(arr[0], arr[1]);

    //dados para tolltip ballon
    var carInternalId = "<?= $car_internal_id; ?>";
    var carModel = "<?= $car_model; ?>";
    var carChassis = '<?= $car_chassis; ?>';

    // Parâmetros do texto que será exibido no clique - TOLLTIP;
    var contentString = '<div style=\"width=220px; height: 220px; text-align: center;\"><h2><img src=\"' + getBaseUrl() + '/themes/seek/images/favicon.png\" title=\"Localização do Carro\" alt=\"Carro\" target=\"_blank\" width="\200px\"></h2>' + '<p style=\"text-align: center; font-weigth: 700;\"> ' + carModel + '<br>' + carInternalId + '<br>Chassis:<br>' + carChassis + '<br>Coord:<br>' + latlng + '</p>' + '<a style=\" text-decoration:none;\" href=\"https://www.google.com/maps/dir/' + latlng + '/' + latlng + '/@' + latlng + ',17z/data=!3m1!4b1\" target=\"_blank\"><b>Como chegar?</b></a></div>';

    var infowindow = new google.maps.InfoWindow({
        content: contentString,
    });

    //OPÇÕES DO MAPA
    var mapOptions = {
        zoom: 17,
        center: myLatlng,
        panControl: true,
        streetViewControlOptions: true,
        zoomControl: true,
        mapTypeId: 'map_style',
        //botoes para alternar tipos de mapa
        mapTypeControlOptions: {
            mapTypeIds: ['map_style', 'roadmap', 'satellite', 'hybrid'],
        }
    };

    // Exibir o mapa na div #map;
    map = new google.maps.Map(document.getElementById("map"), mapOptions);
    map.overlayMapTypes.insertAt(0, new OverlayMap(new google.maps.Size(256, 256)));


    //DESENHA POLYLINE NA TELA

     var points = "<?= $Points; ?>";
    var arr = points.split('|');
    var Coords = arr.map(function (e) {
        var node = e.split(',');
        return new google.maps.LatLng(node[0], node[1]);

    });

//FUNÇÃO PARA INSERIR PIN EXTRA NO MAPA
     // Marcador personalizado;
    var image = getBaseUrl() + '/themes/seek/images/bus_red.png';
    var blue_bus = getBaseUrl() + '/themes/seek/images/bus_yeloww.png';

    Coords.forEach(function (coord, i) {

        var newMarker = new google.maps.Marker({
        position: coord,
        map: map,
        label: {text: 'Ponto ' + i, color: '#fff', fontWeight: 'bold'},
        icon: blue_bus,
        title: 'Seek How - Rastreabilidade Total',
        animation: google.maps.Animation.DROP


    });


         // Parâmetros do texto que será exibido no clique - TOLLTIP;
    var contentPartial = '<div style=\"width=220px; height: 220px; text-align: center;\"><h2><img src=\"' + getBaseUrl() + '/themes/seek/images/favicon.png\" title=\"Localização do Carro\" alt=\"Carro\" target=\"_blank\" width="\200px\"></h2>' + '<p style=\"text-align: center; font-weigth: 700;\"> ' + carModel + '<br>' + carInternalId + '<br>Chassis:<br>' + carChassis + '<br>Coord:<br>' + coord + '</p>' + '<a style=\" text-decoration:none;\" href=\"https://www.google.com/maps/dir/' + latlng + '/' + latlng + '/@' + latlng + ',17z/data=!3m1!4b1\" target=\"_blank\"><b>Como chegar?</b></a></div>';

     var infoPartial = new google.maps.InfoWindow({
        content: contentPartial,
    });
    //Exibir texto ao clicar no ícone;
    google.maps.event.addListener(newMarker, 'click', function () {
        infoPartial.open(map, newMarker);
    });


    })


  var flightPath = new google.maps.Polyline({
    path: Coords,
    geodesic: true,
    strokeColor: '#FF0000',
    strokeOpacity: 1.0,
    strokeWeight: 2
  });


  flightPath.setMap(map);




    // Marcador personalizado;
    //var image = getBaseUrl() + '/themes/seek/images/map_pin.png';

    var marcadorPersonalizado = new google.maps.Marker({
        position: myLatlng,
        map: map,
         label: {text: '-', color: '#fff', fontWeight: 'bold'},
        icon: image,
        title: 'Seek How - Rastreabilidade Total',
        animation: google.maps.Animation.DROP
    });

    //Exibir texto ao clicar no ícone;
    google.maps.event.addListener(marcadorPersonalizado, 'click', function () {
        infowindow.open(map, marcadorPersonalizado);
    });

    // Estilizando o mapa: Criando um array com os estilos
    var styles = [
        {
            stylers: [
                {hue: '#156d97'},
                {saturation: 60},
                {lightness: 35},
                {gamma: 0.2}
            ]
        },
        {
            featureType: "road",
            elementType: "geometry",
            stylers: [
                {lightness: 100},
                {visibility: "simplified"}
            ]
        },
        {
            featureType: "road",
            elementType: "labels"
        }
    ];

    // crio um objeto passando o array de estilos (styles) e definindo um nome para ele;
    var styledMap = new google.maps.StyledMapType(styles, {
        name: "Seek How"
    });
    // Aplicando as configurações do mapa
    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');
}


// Função para carregamento assíncrono
// function loadScript() {
//     var script = document.createElement("script");
//     script.type = "text/javascript";
//     script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyChxxmfnTbsHwFBLiJm2eBOi4xTVBbtGlc&callback=initMap";
//     document.body.appendChild(script);
// }

//window.onload = loadScript;