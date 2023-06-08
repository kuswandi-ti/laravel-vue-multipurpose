<script setup>
    import { reactive, onMounted, ref } from 'vue';
    import { useRouter, useRoute } from 'vue-router';
    import { Form } from 'vee-validate';
    import flatpickr from "flatpickr";
    import 'flatpickr/dist/themes/light.css';

    const form = reactive({
        client_id: '',
        title: '',
        client_id: '',
        start_time: '',
        start_time: '',
        description: '',
    })
    const route = useRoute()
    const router = useRouter()
    const clients = ref()
    const editMode = ref(false)

    const getClient = async (actions) => {
        await axios.get('/api/clients')
        .then((response) => {        
            clients.value = response.data.data
        })
        .catch((error) => {
            actions.setErrors(error.response.data.message)
        })
    }

    const handleSubmit = async (values, actions) => {
        if (editMode.value) {
            updateAppointment(values, actions)
        } else {
            createAppointment(values, actions)
        }
    }

    const createAppointment = async (values, actions) => {
        await axios.post('/api/appointments', form)
        .then((response) => {                
            router.push('/admin/appointments')
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })
        })
        .catch((error) => {
            actions.setErrors(error.response.data.message)
        })
    }

    const updateAppointment = async (values, actions) => {
        await axios.post(`/api/appointments/${route.params.id}`, form)
        .then((response) => {                
            router.push('/admin/appointments')
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })
        })
        .catch((error) => {
            actions.setErrors(error.response.data.message)
        })
    }

    const getAppointment = async (actions) => {
        await axios.get(`/api/appointments/${route.params.id}`)
        .then((response) => {       
            form.title = response.data.data.title
            form.client_id = response.data.data.client_id
            form.start_time = response.data.data.start_time
            form.end_time = response.data.data.end_time
            form.description = response.data.data.description
        })
        .catch((error) => {
            actions.setErrors(error.response.data.message)
        })
    }

    onMounted(() => {
        if (route.name === 'admin.appointments.edit') {
            editMode.value = true
            getAppointment()
        }
        flatpickr(".flatpickr", {
            enableTime: true,
            dateFormat: "Y-m-d H:i:ss",
            time_24hr: true,
            defaultHour: 10,
        })
        getClient()
    })
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <span v-if="editMode">Edit</span>
                        <span v-else>Create</span>
                        Appointment
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/admin/dashboard">Home</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/admin/appointments">Appointments</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <span v-if="editMode">Edit</span>
                            <span v-else>Create</span>
                        </li>
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
                            <Form @submit="handleSubmit" v-slot:default="{ errors }">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input v-model="form.title" type="text" class="form-control" id="title" placeholder="Enter Title" :class="{ 'is-invalid': errors.title}">
                                            <span id="errorTitle" class="invalid-feedback">{{ errors.title }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="client">Client Name</label>
                                            <select v-model="form.client_id" id="client" class="form-control" :class="{ 'is-invalid': errors.client_id}">
                                                <option v-for="client in clients" :key="client.id" :value="client.id">{{ client.first_name }} {{ client.last_name }}</option>
                                            </select>
                                            <span id="errorClient" class="invalid-feedback">{{ errors.client_id }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="date">Start Time</label>
                                            <input v-model="form.start_time" type="text" class="form-control flatpickr" id="start_time" :class="{ 'is-invalid': errors.start_time}">
                                            <span id="errorStartTime" class="invalid-feedback">{{ errors.start_time }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="time">End Time</label>
                                            <input v-model="form.end_time" type="text" class="form-control flatpickr" id="end_time" :class="{ 'is-invalid': errors.end_time}">
                                            <span id="errorEndTime" class="invalid-feedback">{{ errors.end_time }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea v-model="form.description" class="form-control" id="description" rows="3"
                                        placeholder="Enter Description" :class="{ 'is-invalid': errors.description}"></textarea>
                                    <span id="errorDescription" class="invalid-feedback">{{ errors.description }}</span>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>