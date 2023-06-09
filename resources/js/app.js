import './bootstrap';

import 'admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte.min.js';

import Login from './pages/auth/Login.vue';
import Register from './pages/auth/Register.vue';

import Swal from 'sweetalert2';
window.Swal = Swal;

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});
window.Toast = Toast;

import { createApp } from 'vue/dist/vue.esm-bundler.js';

import router from './router';

const app = createApp({})
app.use(router)
app.component('Login', Login)
app.component('Register', Register)
app.mount("#app")
