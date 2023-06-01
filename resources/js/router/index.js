import { createRouter, createWebHistory } from "vue-router";

import Dashboard from '../components/Dashboard.vue';
import ListAppointments from '../components/appointments/ListAppointments.vue';
import ListUsers from '../components/users/ListUsers.vue';
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
        path: '/admin/users',
        name: 'admin.users',
        component: ListUsers,
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