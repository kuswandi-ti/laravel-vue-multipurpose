<script setup>
    import { ref, onMounted, reactive } from 'vue';
    import { Form, Field } from 'vee-validate';
    import * as yup from 'yup';
    import { formatDate } from '../../helper.js'

    const users = ref([])
    const editing = ref(false)
    const formValues = ref()
    const form = ref(null)
    const userIdBeingDelete = ref(null)

    const getUsers = async () => {
        await axios.get('/api/users')
            .then((response) => {
                users.value = response.data.data
                // Toast.fire({
                //     icon: 'success',
                //     title: response.data.message
                // })
            })
            .catch((error) => {
                errors.value = error.response.data;
            });
    }

    const createUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
        password: yup.string().required().min(8),
    })

    const editUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
        // password: yup.string().when((password, schema) => {
        //     return password ? schema.required().min(8) : schema
        // }),
    })

    const addUser = () => {
        editing.value = false        
        $('#userFormModal').modal('show')
        formValues.value = {
            id: '',
            name: '',
            email: '',
            password: '',
        }
    }

    const createUser = async (values, { resetForm, setErrors }) => {
        await axios.post('/api/users', values)
        .then((response) => {                
            users.value.unshift(response.data.data)
            $('#userFormModal').modal('hide')
            resetForm()
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })
        })
        .catch((error) => {
            if (error.response.data.message) {
                setErrors(error.response.data.message)
            }
        })
        // .finnally(() => {
        //     form.value.resetForm()
        // })
    }

    const updateUser = async (values, { setErrors }) => {
        await axios.post('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.findIndex(user => user.id === response.data.data.id)
            users.value[index] = response.data.data
            $('#userFormModal').modal('hide')
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })
        })
        .catch((error) => {
            if (error.response.data.message) {
                setErrors(error.response.data.message)
            }
        })
    }

    const handleSubmit = (values, actions) => {
        if (editing.value) {
            updateUser(values, actions)
        } else {
            createUser(values, actions)
        }
    }

    const editUser = (user) => {
        editing.value = true
        $('#userFormModal').modal('show')
        formValues.value = {
            id: user.id,
            name: user.name,
            email: user.email,
        }
    }

    const confirmUserDelete = (user) => {
        userIdBeingDelete.value = user.id
        $('#deleteUserModal').modal('show')

        // Toast.fire({
        //     title: "Delete this order status?",
        //     text: "Are you sure? You won't be able to revert this!",
        //     type: "warning",
        //     showCancelButton: true,
        //     confirmButtonColor: "#3085d6",
        //     confirmButtonText: "Yes, Delete it!"
        // }).then(function (isConfirm) {
        //     if(isConfirm.value === true) {
        //         deleteUser
        //     }
        // });
    }

    const deleteUser = () => {
        axios.delete('/api/users/' + userIdBeingDelete.value)
        .then((response) => {
            users.value = users.value.filter(user => user.id !== userIdBeingDelete.value)
            $('#deleteUserModal').modal('hide')
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })
        })
    }

    onMounted(() => {
        getUsers();
    })
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">List Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/admin/dashboard">Home</router-link></li>
                        <li class="breadcrumb-item active">List Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <button @click="addUser" type="button" class="btn btn-primary mb-2">
                        Add New User
                    </button>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in users" :key="user.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ formatDate(user.created_at) }}</td>
                                <td>{{ user.role }}</td>
                                <td>
                                    <router-link to="#" @click.prevent="editUser(user)"><i class="fa fa-edit"></i></router-link>&nbsp;
                                    <router-link to="#" @click.prevent="confirmUserDelete(user)"><i class="fa fa-trash text-danger"></i></router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="userFormModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span v-if="editing">Edit Existing User</span>
                        <span v-else>Add New User</span>                        
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema" v-slot="{ errors }" :initial-values="formValues">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <Field name="name" type="text" class="form-control" id="name" placeholder="Enter full name" :class="{ 'is-invalid': errors.name}" />
                            <span id="errorName" class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <Field name="email" type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" :class="{ 'is-invalid': errors.email}" />
                            <span id="errorEmail" class="invalid-feedback">{{ errors.email }}</span>
                        </div>
                        <div class="form-group" v-if="!editing">
                            <label for="password">Password</label>
                            <Field name="password" type="password" class="form-control" id="password" placeholder="Password" :class="{ 'is-invalid': errors.password}" />
                            <span id="errorPassword" class="invalid-feedback">{{ errors.password }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="deleteUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span>Delete User</span>                        
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Are you sure you want to delete this user ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button @click.prevent="deleteUser" type="button" class="btn btn-danger">Yes</button>
                </div>
            </div>
        </div>
    </div>
</template>