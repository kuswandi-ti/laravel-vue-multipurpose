<script setup>
    import { ref, onMounted } from 'vue';

    const settings = ref([])

    const getSettings = async () => {
        await axios.get('/api/settings')
        .then((response) => {
            settings.value = response.data
        })
        .catch((error) => {
            errors.value = error.response.data;
        })
    }

    const updateSettings = async () => {
        await axios.post('/api/settings', settings.value)
            .then((response) => {
                Toast.fire({
                    icon: 'success',
                    title: response.data.message
                })
            })
            .catch((error) => {
                errors.value = error.response.data;
            })
    }

    onMounted(() => {
        getSettings()
    })
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Setting</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/admin/dashboard">Home</router-link></li>
                        <li class="breadcrumb-item active">Update Setting</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <form @submit.prevent="updateSettings()">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Update General Setting</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="appName">App Display Name</label>
                                    <input v-model="settings.app_name" type="text" class="form-control" id="appName" placeholder="Enter app display name">
                                </div>
                                <div class="form-group">
                                    <label for="dateFormat">Date Format</label>
                                    <select v-model="settings.date_format" class="form-control">
                                        <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                                        <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                                        <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                        <option value="Month DD, YYYY">Month DD, YYYY</option>
                                        <option value="DD Month YYYY">DD Month YYYY</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="paginationLimit">Pagination Limit</label>
                                    <input v-model="settings.pagination_limit" type="text" class="form-control" id="paginationLimit" placeholder="Enter pagination limit">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i>Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
