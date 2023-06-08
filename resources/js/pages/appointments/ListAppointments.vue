<script setup>
    import { ref, onMounted, computed } from 'vue';
    import Swal from 'sweetalert2';

    const appointments = ref([])
    const appointmentStatus = ref([])
    const selectedStatus = ref([])

    const getAppointments = async (status) => {
        selectedStatus.value = status
        const params = {};
        if (status) {
           params.status = status 
        }

        await axios.get('/api/appointments', {
            params: params
        })        
        .then((response) => {
            appointments.value = response.data.data
        })
        .catch((error) => {
            errors.value = error.response.data;
        })
    }

    const getAppointmentsStatus = async () => {
        await axios.get('/api/appointment-status')
        .then((response) => {
            appointmentStatus.value = response.data.data
        })
        .catch((error) => {
            errors.value = error.response.data;
        })
    }

    const appointmentsCount = computed(() => {
        return appointmentStatus.value.map(status => status.count).reduce((acc, value) => acc + value, 0)
    })

    const deleteAppointment = (id) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/api/appointments/${id}`)
                .then((response) => {
                    appointments.value.data = appointments.value.data.filter(appointment => appointment.id !== id)
                    Swal.fire(
                        'Deleted!',
                        response.data.message,
                        'success'
                    )
                })
                .catch((error) => {
                    errors.value = error.response.data;
                })                
            }
        })
    }

    onMounted(() => {
        getAppointments()
        getAppointmentsStatus()
    })
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">List Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/admin/dashboard">Home</router-link></li>
                        <li class="breadcrumb-item active">List Appointments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">                    
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <router-link to="/admin/appointments/create">
                                        <button class="btn btn-primary"><i class="fa fa-plus-circle mr-1"></i> 
                                            Add New Appointment
                                        </button>
                                    </router-link>
                                </div>
                                <div class="btn-group">
                                    <button @click="getAppointments()" type="button" class="btn" :class="[typeof selectedStatus === 'undefined' ? 'btn-secondary' : 'btn-default']">
                                        <span class="mr-1">All</span>
                                        <span class="badge badge-pill badge-info">{{ appointmentsCount }}</span>
                                    </button>

                                    <button 
                                        v-for="status in appointmentStatus" 
                                        @click="getAppointments(status.value)" 
                                        type="button" 
                                        class="btn" 
                                        :class="[selectedStatus === status.value ? 'btn-secondary' : 'btn-default']">
                                        <span class="mr-1">{{ status.name }}</span>
                                        <span class="badge badge-pill" :class="`badge-${status.color}`">{{ status.count }}</span>
                                    </button>
                                </div>
                            </div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">#</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Client Name</th>
                                        <th scope="col">Start Time</th>
                                        <th scope="col">End Time</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
                                        <td class="text-center">{{ index + 1 }}</td>
                                        <td>{{ appointment.title }}</td>
                                        <td>{{ appointment.client.first_name }} {{ appointment.client.last_name }}</td>
                                        <td class="text-center">{{ appointment.start_time }}</td>
                                        <td class="text-center">{{ appointment.end_time }}</td>
                                        <td class="text-center">
                                            <span class="badge" :class="`badge-${appointment.status.color}`">{{ appointment.status.name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <router-link :to="`/admin/appointments/${appointment.id}`">
                                                <i class="fa fa-edit mr-2"></i>
                                            </router-link>
                                            <a href="#" @click.prevent="deleteAppointment(appointment.id)">
                                                <i class="fa fa-trash text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>