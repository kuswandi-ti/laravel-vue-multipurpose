import { createRouter, createWebHistory } from "vue-router";

import Dashboard from '../components/Dashboard.vue';
import ListAppointments from '../components/appointments/ListAppointments.vue';
import AppointmentForm from '../components/appointments/AppointmentForm.vue';
import UserList from '../components/users/UserList.vue';
import UpdateSetting from '../components/settings/UpdateSetting.vue';
import UpdateProfile from '../components/profile/UpdateProfile.vue';

export const routes = [
    {        
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
    },
    {        
        path: '/admin/appointments',
        name: 'admin.appointments',
        component: ListAppointments,
    },
    {        
        path: '/admin/appointments/create',
        name: 'admin.appointments.create',
        component: AppointmentForm,
    },
    {        
        path: '/admin/appointments/:id',
        name: 'admin.appointments.edit',
        component: AppointmentForm,
    },
    {        
        path: '/admin/users',
        name: 'admin.users',
        component: UserList,
    },
    {        
        path: '/admin/settings',
        name: 'admin.settings',
        component: UpdateSetting,
    },
    {        
        path: '/admin/profile',
        name: 'admin.profile',
        component: UpdateProfile,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

export default router