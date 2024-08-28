<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/logos/logo.svg">
    <link rel="icon" type="image/png" href="assets/img/logos/logo.svg">
    <title>Mapbox zonificacion</title>
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.5.1/mapbox-gl.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.5.1/mapbox-gl.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href=" assets/css/material-kit.css?v=3.0.4" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
    <style>
        .menu { z-index: 9999; position: absolute; bottom: 10px; left: 10px; display: none;}
        #map { position: absolute; top: 0; bottom: 0; width: 100%;}
        .mapboxgl-ctrl-bottom-right, .mapboxgl-ctrl-bottom-left{display: none;}

        .ubicacion{position: fixed; bottom: 10px; right:10px; z-index: 100; border-radius:50px; width: 40px; height: 40px; background:black; border:1px solid white;}
    </style>
</head>
<body>
    <?php
        include 'componentes/nav.php';
    ?>

    <div id="intro11" class="card row text-center align-self-center opacity-hover-7" style="position: fixed; bottom: 10px; left:20px; z-index: 100;">

        <div class="col-12 mb-n2 mt-2">


            <!-- Example single danger button -->
            <div class="btn-group dropdown-right">
                <button id="seleccion_pagina" class="btn btn-success btn-sm dropdown-toggle" value="paso1" data-bs-toggle="dropdown" aria-expanded="false">
                    <span id="">Lotes</span>
                </button>
                <ul class="dropdown-menu text-center mt-n2 pt-1 mb-2 ms-n2" style="min-width: 49%; width: 19rem;">

                    <?php
                        include "conexion.php";
                        
                        
                        // Consultar las muestras del lote específico
                        $sql = "SELECT id_lote, nombre_lote, coordenada1, coordenada2, color_punto FROM lotes";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "

                                <div class='bg-" . $row["color_punto"] . " text-white btn mt-2 mb-0 btn-sm' style='background:" . $row["color_punto"] . ";'>" . $row["id_lote"] . ". " . $row["nombre_lote"] . "</div>
                                
                                ";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No se encontraron lotes</td></tr>";
                        }
                    ?>
                </ul>
            </div>

            
        </div>
    </div>

    <div class="cursor-pointer bg-success ubicacion">
    <span class="material-symbols-outlined p-1" style="font-size: 30px; color: white;">my_location</span>
    </div>

    <div id="map"></div>

    
    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoiamNhbWVsbzYyNSIsImEiOiJjbGR1enBwM24wNXRyM29ubzBjZmY5aXdvIn0.hzI9ZFtUSUhqIm_dWoSJrg';
        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/jcamelo625/clemt2qhk000601s4prht0a9e',
            center: [-73.233450, 10.399234],
            zoom: 14.1
        });

        map.on('load', function() {
            fetch('obtener_lotes.php')
            .then(response => response.json())
            .then(data => {
                map.addLayer({
                    "id": "puntos-de-cultivo",
                    "type": "circle",
                    "source": {
                        "type": "geojson",
                        "data": data
                    },
                    "paint": {
                        "circle-color": [
                            "match",
                            ["get", "type"],
                            // Aquí tienes que mapear todos los colores de los puntos de la base de datos
                            ...data.features.map(feature => [feature.properties.type, feature.properties.color]).flat(),
                            "#ccc"
                        ],
                        "circle-radius": 10,
                        "circle-stroke-width": 2,
                        "circle-stroke-color": "#fff"
                    }
                });
                
                map.addLayer({
                "id": "puntos-de-cultivo-labels",
                "type": "symbol",
                "source": {
                    "type": "geojson",
                    "data": data
                },
                "layout": {
                    "text-field": ["get", "id"], // Usar el campo id_lote como texto
                    "text-font": ["Open Sans Regular"],
                    "text-size": 13,
                    "text-offset": [0, 0], // Ajustar la posición del texto si es necesario
                    "text-anchor": "center", // Alinear el texto al centro del punto
                },
                "paint": {
                    "text-color": "#000000", // Color del texto
                    "text-halo-color": "white", // Color de la sombra
                    "text-halo-width": 1 // Tamaño de la sombra
                }
            });

                // Agregar eventos de clic y cambio de cursor...
                map.on('click', 'puntos-de-cultivo', function (e) {
                    var feature = e.features[0];
                    var nombre = feature.properties.type;
                    var id_lote = feature.properties.id;

                    Swal.fire({
                        title: `${nombre}`,
                        html: `
                        Información sobre el lote, ejemplo:
                            <br>
                        cuantos estudios se ha realizado
                        <br> estadisticas y mas.

                        `,
                        showCloseButton: true,
                        showCancelButton: false,
                        showConfirmButton: false,
                    });
                });

                map.on('mouseenter', 'puntos-de-cultivo', function () {
                    map.getCanvas().style.cursor = 'pointer';
                });

                map.on('mouseleave', 'puntos-de-cultivo', function () {
                    map.getCanvas().style.cursor = '';
                });
            });
        });
    </script>

      <!--   Core JS Files   -->
  <script src=" assets/js/core/popper.min.js" type="text/javascript"></script>
  <script src=" assets/js/core/bootstrap.min.js" type="text/javascript"></script>
  <script src=" assets/js/plugins/perfect-scrollbar.min.js"></script>
  <!-- Control Center for Material UI Kit: parallax effect, scripts for the example pages etc -->
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDTTfWur0PDbZWPr7Pmq8K3jiDp0_xUziI"></script>
  <script src=" assets/js/material-kit.min.js?v=3.0.4" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/4.2.2/intro.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="assets/js/script.js"></script>
</body>
</html>
