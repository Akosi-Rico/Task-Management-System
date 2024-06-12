import './bootstrap';


//require('datatables.net');
//import './datatable';
import DataTable from 'datatables.net-dt';

import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import './datatable.pipiline.js';

import { createApp } from 'vue';
import index from './components/index.vue';
import login from './components/auth/login.vue';
import register from './components/auth/register.vue';



// Assign jQuery to the global window object if needed


// Attach the pipeline function to jQuery's DataTable
//$.fn.dataTable.pipeline = pipeline;

createApp({})
  .component('parent', index)
  .component('login', login)
  .component('register', register)
  .mount('#app');


