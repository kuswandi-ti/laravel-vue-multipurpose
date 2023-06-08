<script setup>
    import { ref, onMounted, watch } from 'vue';
    import { Form, Field } from 'vee-validate';
    import * as yup from 'yup';
    import { debounce } from 'lodash';
    import { Bootstrap4Pagination } from 'laravel-vue-pagination';

    import UserListItem from './UserListItem.vue';

    const users = ref({
        'data': []
    })
    const editing = ref(false)
    const formValues = ref()
    const form = ref(null)
    const searchQuery = ref(null)
    const selectedUsers = ref([])
    const selectAll = ref(false)
    const userIdBeingDeleted = ref(null)

    const createUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
        password: yup.string().required().min(8),
    })

    const editUserSchema = yup.object({
        name: yup.string().required(),
        email: yup.string().email().required(),
    })

    const handleSubmit = (values, actions) => {
        if (editing.value) {
            updateUser(values, actions)
        } else {
            createUser(values, actions)
        }
    }
    
    const getUsers = async (page = 1) => {
        await axios.get(`/api/users?page=${page}`, {
            params: {
                query: searchQuery.value
            }
        })
        .then((response) => {
            users.value = response.data.data
            selectedUsers.value = []
            selectAll.value = false
        })
        .catch((error) => {
            selectedUsers.value = []
            selectAll.value = false
        })
    }    

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
            users.value.data.unshift(response.data.data)
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

    const updateUser = async (values, actions) => {
        await axios.post('/api/users/' + formValues.value.id, values)
        .then((response) => {
            // const index = users.value.findIndex(user => user.id === response.data.data.id)
            // users.value[index] = response.data.data
            getUsers()
            $('#userFormModal').modal('hide')
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })            
        })
        .catch((error) => {
            actions.setErrors(error.response.data.message)
        })
    }

    watch(searchQuery, debounce(() => {
        getUsers()
    }, 300))

    const toggleSelection = (user) => {
        const index = selectedUsers.value.indexOf(user.id)
        if (index === -1) {
            selectedUsers.value.push(user.id)
        } else {
            selectedUsers.value.splice(index, 1)
        }
    }

    const selectAllUsers = () => {
        if (selectAll.value) {
            selectedUsers.value = users.value.data.map(user => user.id)
        } else {
            selectedUsers.value = []
        }
        console.log(selectedUsers.value)
    }

    const confirmUserDeletion = (id) => {
        userIdBeingDeleted.value = id
        $('#deleteUserModal').modal('show')
    }

    const deleteUser = (actions) => {
        axios.delete('/api/users/' + userIdBeingDeleted.value)
        .then((response) => {
            $('#deleteUserModal').modal('hide')
            users.value.data = users.value.data.filter(user => user.id !== userIdBeingDeleted.value)
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })
        })
        .catch((error) => {
            actions.setErrors(error.response.data.message)
        })
    }

    const bulkDelete = async (actions) => {
        await axios.delete('/api/users', {
            data: {
                ids: selectedUsers.value
            }
        })
        .then((response) => {
            users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id))
            selectedUsers.value = []
            selectAll.value = false
            getUsers()
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })
        })
        .catch((error) => {
            actions.setErrors(error.response.data.message)
        })
    }
    
    onMounted(() => {
        getUsers()
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
                    <div class="d-flex justify-content-between">
                        <div>
                            <button @click="addUser" type="button" class="btn btn-primary mb-2">
                                <i class="fa fa-plus-circle mr-1"></i> Add New User
                            </button>
                            <button v-if="selectedUsers.length > 0" @click="bulkDelete" type="button" class="btn btn-danger mb-2 ml-2">
                                <i class="fa fa-trash mr-1"></i> Delete Selected
                            </button>
                            <span v-if="selectedUsers.length > 0" class="ml-2"> 
                                Selected <span class="text-danger">{{ selectedUsers.length }}</span> users
                            </span>
                        </div>
                        <div>
                            <input v-model="searchQuery" type="text" class="form-control" placeholder="Search..." />
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllUsers" /></th>
                                <th style="width: 10px">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.data.length > 0">
                            <UserListItem v-for="(user, index) in users.data" 
                                :key="user.id" 
                                :user=user
                                :index=index
                                @edit-user="editUser"
                                @confirm-user-deletion="confirmUserDeletion"
                                @toggle-selection="toggleSelection"
                                :select-all="selectAll"
                            />
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">
                                    No results found...
                                </td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>
            </div>
            <Bootstrap4Pagination 
                :data="users"
                @pagination-change-page="getUsers"
            />
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