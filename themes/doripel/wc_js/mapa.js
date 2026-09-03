function initialize() {
    // Exibir mapa;
    var myLatlng = new google.maps.LatLng(-28.230553, -51.515820);
    var mapOptions = {
        zoom: 17,
        center: myLatlng,
        panControl: true,
        streetViewControlOptions: true,
        zoomControl: true,

        // mapTypeId: google.maps.MapTypeId.ROADMAP
        mapTypeControlOptions: {
            mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style'],
        }
    }

    // Parâmetros do texto que será exibido no clique;
    var contentString = '<div style=\"width=200px; height: 180px; text-align: center;\"><h2><img src="https://localhost/doripel/themes/doripel/images/logo.png" title="Localização da Doripel Móveis" alt="Doripel Móveis" target="_blank" width="200px"></h2>' +
    '<p style=\"text-align: center; font-weigth: 700;\"> Rua Júlio Vanzin, 1600  <br>Bairro Industrial III - Lagoa Vermelha - RS</p>' +
    '<a style=\" text-decoration:none;\" href="https://goo.gl/maps/mmLkDXgWqea1hbC77" target="_blank"><b>Como chegar?</b></a></div>';


    var infowindow = new google.maps.InfoWindow({
        content: contentString,

    });


    // Exibir o mapa na div #mapa;
    var map = new google.maps.Map(document.getElementById("map"), mapOptions);


    // Marcador personalizado;
    var image = 'https://localhost/doripel/themes/doripel/images/icons/map_pin.png';
    console.log(image);
    var marcadorPersonalizado = new google.maps.Marker({
        position: myLatlng,
        map: map,
        icon: image,
        title: 'Doripel Móveis',
        animation: google.maps.Animation.DROP
    });


//   // Exibir texto ao clicar no ícone;
    google.maps.event.addListener(marcadorPersonalizado, 'click', function () {
        infowindow.open(map, marcadorPersonalizado);
    });


    // Estilizando o mapa;
    // Criando um array com os estilos
    var styles = [
        {
            stylers: [
                {hue: '#6699cc'},
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
        name: "Doripel Móveis"
    });

    // Aplicando as configurações do mapa
    map.mapTypes.set('map_style', styledMap);
    map.setMapTypeId('map_style');

}

//Função para carregamento assíncrono
function loadScript() {
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyD-_PXRTIVSOUFA2L5o06Q2giinoq_pKfY&callback=initialize";
    document.body.appendChild(script);
}

window.onload = loadScript;
