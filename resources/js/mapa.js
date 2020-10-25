// when the docs use an import:
import { Marker } from 'leaflet';
import { OpenStreetMapProvider } from 'leaflet-geosearch';
const provider = new OpenStreetMapProvider();

document.addEventListener('DOMContentLoaded', () => {

    if(document.querySelector('#mapa')){
        const lat = document.querySelector('#lat').value === '' ?  -33.45694 : document.querySelector('#lat').value;
        const lng = document.querySelector('#lng').value === '' ?  -70.64827 : document.querySelector('#lng').value;
    
        const mapa = L.map('mapa').setView([lat, lng], 10);

        //eliminar pines previos
        let markers = new L.FeatureGroup().addTo(mapa);
    
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mapa);
    
        let marker;
    
        // agregar el pin
        marker = new L.marker([lat, lng], {
            draggable:true, 
            autoPan: true,
        }).addTo(mapa);

        //agregar pin a las capas
        markers.addLayer(marker);

        //geocode service
        const geocodeService = L.esri.Geocoding.geocodeService();

        //buscador de direcciones
        const buscador = document.getElementById('formbuscador'); 
        buscador.addEventListener('blur', buscarDireccion); 


        reubicarPin(marker);

        function reubicarPin(marker){
            //detectar movimiento de marker
            marker.on('moveend', function(e){
                marker = e.target; 
                const posicion = marker.getLatLng();

                //centrar automaticamente
                mapa.panTo(new L.LatLng(posicion.lat, posicion.lng))
                

                //reverse geocoding, cuando el usuario reubica el pin
                geocodeService.reverse().latlng(posicion, 10).run(function(error, resultado){
                    //console.log(resultado.address, error); 

                    marker.bindPopup(resultado.address.LongLabel); 
                    marker.openPopup(); 

                    //llenar los campos
                    llenarInputs(resultado);

                }); 
            }); 
        }



        function buscarDireccion(e){

            if(e.target.value.length > 10){
                provider.search({query: e.target.value + ' Santiago CL '})
                    .then(resultado => {
                        if(resultado[0]){

                            //limpiar pines previos
                            markers.clearLayers();

                            //reverse geocoding, cuando el usuario reubica el pin
                            geocodeService.reverse().latlng(resultado[0].bounds[0], 10).run(function(error, resultado){
                                //llenar inputs 
                                llenarInputs(resultado);

                                //centrar mapa
                                mapa.setView(resultado.latlng); 

                                //agregar pin 
                                marker = new L.marker(resultado.latlng, {
                                    draggable:true, 
                                    autoPan: true,
                                }).addTo(mapa);

                                //asignar el contenedor de markers el nuevo pin
                                markers.addLayer(marker); 
                                //mover pin

                                reubicarPin(marker);

                            }); 
                        }
                    })
                    .catch(error => {
                        //console.log(error);
                    })
            }

        }


        function llenarInputs(resultado){
            //console.log(resultado.address);
            document.querySelector('#direccion').value = resultado.address.Address || '';
            document.querySelector('#colonia').value = resultado.address.District || '';
            document.querySelector('#lat').value = resultado.latlng.lat || '';
            document.querySelector('#lng').value = resultado.latlng.lng || '';
        }
    }

});