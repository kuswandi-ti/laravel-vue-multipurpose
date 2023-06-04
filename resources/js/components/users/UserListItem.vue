<script setup>
    import { ref } from 'vue';
    import { formatDate } from '../../helper.js';
    
    const emit = defineEmits(['userDeleted', 'editUser', 'confirmUserDeletion'])
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

    const props = defineProps({
        user: Object,
        index: Number,
        selectAll: Boolean
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

    const toggleSelection = () => {
        emit('toggleSelection', props.user)
    }
</script>

<template>
    <tr>
        <th class="text-center"><input type="checkbox" :checked="selectAll" @change="toggleSelection" /></th>
        <td class="text-center">{{ index + 1 }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td class="text-center">{{ formatDate(user.created_at) }}</td>
        <td class="text-center">
            <select class="form-control" @change="changeRole(user, $event.target.value)">
                <option v-for="role in roles" :value="role.value" :selected="(user.role == role.name)">{{ role.name }}</option>
            </select>
        </td>
        <td class="text-center">
            <router-link to="#" @click.prevent="$emit('editUser', user)"><i class="fa fa-edit"></i></router-link>&nbsp;
            <router-link to="#" @click.prevent="$emit('confirmUserDeletion', user.id)"><i class="fa fa-trash text-danger"></i></router-link>
        </td>
    </tr>    
</template>