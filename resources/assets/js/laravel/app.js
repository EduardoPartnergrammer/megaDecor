
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import Vuex from 'vuex';
import StoreData from './store';
import VueFuse from 'vue-fuse';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
Vue.use(VueFuse);

Vue.use(Vuex);

const store = new Vuex.Store(StoreData);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

//Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('nuevo-cliente-component', require('./components/NuevoClienteComponent.vue').default);
Vue.component('nueva-tarea-component', require('./components/NuevaTareaComponent.vue').default);
Vue.component('categoria-tarea-component', require('./components/CategoriasTareas.vue').default);
Vue.component('categoria-evento-component', require('./components/CategoriasEvento.vue').default);
Vue.component('task-list-component', require('./components/TaskListComponent.vue').default);
Vue.component('tipo-empresa-component', require('./components/TipoEmpresaComponent.vue').default);
Vue.component('como-supo-component', require('./components/ComoSupoComponent.vue').default);
Vue.component('crear-presupuesto-component', require('./components/CrearPresupuestoComponent.vue').default);
Vue.component('settings-master-component', require('./components/SettingsMasterComponent.vue').default);
Vue.component('editar-presupuesto-component', require('./components/EditarPresupuestoComponent.vue').default);
Vue.component('ver-presupuesto-component', require('./components/VerPresupuestoComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

var EventBus = new Vue;
const app = new Vue({
    el: '#app',
    store,
    
});


document.addEventListener('DOMContentLoaded', function() {
var calendarEl = document.getElementById('calendar');

var calendar = new Calendar(calendarEl, {
  plugins: [ dayGridPlugin ],
  dateClick: function() {
    alert('a day has been clicked!');
  },
});


calendar.batchRendering(function() {
    
    //Obtenemos todas las tareas
    let URL = '/tareas/obtener-tareas';
    axios.get(URL).then((response) => {
        var tareas = response.data;
       
        
        //tareas.forEach(function(element) {  
         // });

          calendar.changeView('dayGridMonth');
          calendar.addEvent({ title: 'Llamada', start: '2019-09-03' });
          calendar.addEvent({ title: 'Visita', start: '2019-09-02', color: '#F19A65' });
          calendar.addEvent({ title: 'Evento', start: '2019-09-02', color: '#65F198' });
          calendar.addEvent({ title: 'Llamada', start: '2019-09-02' });
          calendar.addEvent({ title: 'Evento', start: '2019-09-04', color: '#65F198' });
          calendar.addEvent({ title: 'Visita', start: '2019-09-16', color: '#F19A65' });
          
          calendar.addEvent({ title: 'Visita', start: '2019-09-15', color: '#F19A65' });
          calendar.addEvent({ title: 'Llamada', start: '2019-09-12' });
          calendar.addEvent({ title: 'Evento', start: '2019-09-12', color: '#65F198' });
          calendar.addEvent({ title: 'Visita', start: '2019-09-15', color: '#F19A65' });


          calendar.addEvent({ title: 'Llamada', start: '2019-10-05' });
          calendar.addEvent({ title: 'Visita', start: '2019-10-12', color: '#F19A65' });
          calendar.addEvent({ title: 'Evento', start: '2019-10-21', color: '#65F198' });
          calendar.addEvent({ title: 'Llamada', start: '2019-10-13' });
          calendar.addEvent({ title: 'Evento', start: '2019-10-14', color: '#65F198' });
          calendar.addEvent({ title: 'Visita', start: '2019-10-18', color: '#F19A65' });
          
          calendar.addEvent({ title: 'Visita', start: '2019-10-20', color: '#F19A65' });
          calendar.addEvent({ title: 'Llamada', start: '2019-10-10' });
          calendar.addEvent({ title: 'Evento', start: '2019-10-02', color: '#65F198' });
          calendar.addEvent({ title: 'Visita', start: '2019-10-05', color: '#F19A65' });
          
          


            

        
    });
    
  });


calendar.render();

});



 

