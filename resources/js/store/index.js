import Vue from 'vue'; 
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        cafes: [], 
        restaurants: [],
        hotels: [] , 
        establecimiento: {}, 
        establecimientos: [], 
        categorias: [], 
        categoria: ''

    }, 
    mutations:{
        AGREGAR_CAFES(state, cafes){
            state.cafes = cafes; 
        }, 
        AGREGAR_RESTAURANT(state, restaurants){
            state.restaurants = restaurants; 
        }, 
        AGREGAR_HOTEL(state, hotels){
            state.hotels = hotels; 
        }, 
        AGREGAR_ESTABLECIMIENTO(state, establecimiento){
            state.establecimiento = establecimiento;
        }, 
        AGREGAR_ESTABLECIMIENTOS(state, establecimientos){
            state.establecimientos = establecimientos;
        }, 
        AGREGAR_CATEGORIAS(state, categorias){
            state.categorias = categorias;
        }, 
        SELECCIONAR_CATEGORIA(state, categoria){
            state.categoria = categoria;
        }
    }, 
    getters:{
        obtenerEstablecimiento: state => {
            return state.establecimiento
        }, 
        obtenerImagenes: state => {
            return state.establecimiento.imagenes
        }, 
        obtenerEstablecimientos: state => {
            return state.establecimientos
        }, 
        obtenerCategorias: state => {
            return state.categorias;
        }, 
        obtenerCategoria: state => {
            return state.categoria;
        }
    }
}); 