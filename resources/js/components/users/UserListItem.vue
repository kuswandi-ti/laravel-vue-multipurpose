<script setup>
    import { ref } from 'vue';
    import { formatDate } from '../../helper.js';

    const userIdBeingDeleted = ref(null)
    const emit = defineEmits(['userDeleted', 'editUser'])
    const roles = ref([
        {
            name: 'ADMIN',
            value: 1
        },
        {
            name: 'USER',
            value: 2
        }
    ])

    defineProps({
        user: Object,
        index: Number,
    })

    const changeRole = (user, role) => {
        axios.post(`/api/users/${user.id}/change-role`, {
            role: role
        })
        .then((response) => {
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })
        })
    }

    const confirmUserDelete = (user) => {
        userIdBeingDeleted.value = user.id
        $('#deleteUserModal').modal('show')
    }

    const deleteUser = () => {
        axios.delete('/api/users/' + userIdBeingDeleted.value)
        .then((response) => {
            $('#deleteUserModal').modal('hide')
            emit('userDeleted', userIdBeingDeleted.value)
            Toast.fire({
                icon: 'success',
                title: response.data.message
            })
        })
    }
</script>

<template>
    <tr>
        <td>{{ index + 1 }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ formatDate(user.created_at) }}</td>
        <td>
            <select class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :value="role.value" :selected="(user.role == role.name)">{{ role.name }}</option>
            </select>
        </td>
        <td>
            <router-link to="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></router-link>&nbsp;
            <router-link to="#" @click.prevent="confirmUserDelete(user)"><i class="fa fa-trash text-danger"></i></router-link>
        </td>
    </tr>

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