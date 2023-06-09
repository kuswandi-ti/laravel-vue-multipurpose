<script setup>
    import { ref } from 'vue';
    import { Form, Field } from 'vee-validate';
    import * as yup from 'yup';

    const errorMessage = ref('')
    const loading = ref(false)
    const createSchema = yup.object({
        name: yup.string().required().max(255),
        email: yup.string().required().email(),
        password: yup.string().required().min(8),
        c_password: yup.string().required('confirmation password is a required field').min(8).oneOf([yup.ref('password'), null], 'Password and Confirmation Password are not the same')
    })

    const handleSubmit = async (values, actions) => {
        loading.value = true
        errorMessage.value = ''
        await axios.post('/api/register', values)
        .then((response) => {
            if (response.data.success) { 
                window.location.href="/admin/dashboard"
                Toast.fire({
                    icon: 'success',
                    title: response.data.message
                })
            } else {
                errorMessage.value = response.data.message
            }
        })
        .catch((error) => {
            actions.setErrors(error.response.data.message)
        })
        .finally(() => {
            loading.value = false
        })
    }
</script>

<template>
    <div class="register-box">
        <div class="register-logo">
            <span>Multipurpose Module Laravel Vue</span>
        </div>

        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <span class="h1">Register</span>
            </div>
            <div class="card-body">
                <div v-if="errorMessage" class="alert alert-danger" role="alert">
                    {{ errorMessage }}
                </div>
                <Form @submit="handleSubmit" :validation-schema="createSchema" v-slot="{ errors }">
                    <div class="input-group mb-3">
                        <Field name="name" type="text" class="form-control" placeholder="Name" :class="{ 'is-invalid': errors.name}" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        <span id="errorName" class="invalid-feedback">{{ errors.name }}</span>
                    </div>
                    <div class="input-group mb-3">
                        <Field name="email" type="email" class="form-control" placeholder="Email" :class="{ 'is-invalid': errors.email}" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <span id="errorEmail" class="invalid-feedback">{{ errors.email }}</span>
                    </div>
                    <div class="input-group mb-3">
                        <Field name="password" type="password" class="form-control" placeholder="Password" :class="{ 'is-invalid': errors.password}" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <span id="errorPassword" class="invalid-feedback">{{ errors.password }}</span>
                    </div>
                    <div class="input-group mb-3">
                        <Field name="c_password" type="password" class="form-control" placeholder="Retype password" :class="{ 'is-invalid': errors.c_password}" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <span id="errorCPassword" class="invalid-feedback">{{ errors.c_password }}</span>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <a href="/login" class="text-center">I already have a user ?</a>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
                                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span v-else> Register</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>