const { default: axios } = require("axios");



document.addEventListener('DOMContentLoaded', () => {

    if(document.querySelector('#dropzone')){
        Dropzone.autoDiscover = false; 
        const dropzone = new Dropzone('div#dropzone', {
            url: '/imagenes/store', 
            dictDefaultMessage: 'Sube hasta 10 imagenes', 
            maxFiles: 10,
            required: true, 
            acceptedFiles: '.png, .jpg, .gif, .bimp, .jpeg', 
            addRemoveLinks: true,
            dictRemoveFile: 'Eliminar Imagen',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
            }, 
            init: function(){
                const galeria = document.querySelectorAll('.galeria'); 
                
                if(galeria.length > 0){
                    galeria.forEach(imagen => {
                        const imagenPublicada = {}; 
                        imagenPublicada.size = 1;

                        imagenPublicada.name = imagen.value;
                        imagenPublicada.nombreServidor = imagen.value;

                        this.options.addedfile.call(this, imagenPublicada);
                        this.options.thumbnail.call(this, imagenPublicada, `/storage/${imagenPublicada.name}`);

                        imagenPublicada.previewElement.classList.add('dz-success'); 
                        imagenPublicada.previewElement.classList.add('dz-complete'); 
                    }); 
                }
            },
            success: function(file, respuesta){
                file.nombreServidor = respuesta.archivo
            }, 
            sending: function(file, xhr, formData){
                formData.append('uuid', document.querySelector('#uuid').value);
                console.log('enviando');
            }, 
            removedfile: function(file, respuesta){
                const params = {
                    imagen: file.nombreServidor, 
                    uuid: document.querySelector('#uuid').value
                }
                axios.post('/imagenes/destroy',params)
                    .then(respuesta => {
                        console.log(respuesta);

                        //eliminar del dom
                        file.previewElement.parentNode.removeChild(file.previewElement); 
                    }) 
            }  
        }); 
    }


}); 