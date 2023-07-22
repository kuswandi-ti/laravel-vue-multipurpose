<script setup>
    import { ref } from 'vue';
    import { Form, Field } from 'vee-validate';
    import * as yup from 'yup';

    const errorMessage = ref('')
    const loading = ref(false)
    const createSchema = yup.object({
        email: yup.string().email().required(),
        password: yup.string().required().min(8),
    })

    const handleSubmit = async (values, actions) => {
        loading.value = true
        errorMessage.value = ''
        await axios.post('/api/login', values)
        .then((response) => {
            if (response.data.success) {
                Toast.fire({
                    icon: 'success',
                    title: response.data.message
                })
                window.location.href="/admin/dashboard"                
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
    <div class="login-box">
        <div class="register-logo">
            <span>Multipurpose Module Laravel Vue</span>
        </div>
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <span class="h1">Login</span>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h6>Email : <strong>kuswandi.ti@gmail.com</strong></h6>
                    <h6>Password : <strong>password</strong></h6>
                </div>
                <div v-if="errorMessage" class="alert alert-danger" role="alert">
                    {{ errorMessage }}
                </div>
                <Form @submit="handleSubmit" :validation-schema="createSchema" v-slot="{ errors }">
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
                    <div class="row">
                        <div class="col-8">
                            <p class="mb-0">
                                <a href="/register" class="text-center">Register a new user</a>
                            </p>
                        </div>

                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block" :disabled="loading">
                                <div v-if="loading" class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span v-else> Login</span>
                            </button>
                        </div>
                    </div>
                </Form>
            </div>
        </div>
    </div>
</template>