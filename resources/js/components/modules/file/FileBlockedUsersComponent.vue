<template>
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title text-danger"><b>Bloqueados</b>
                    <span v-show="blocked_users.length" class="right badge badge-danger">{{ blocked_users.length }}</span>
                </h3>
                <div class="card-tools">
                    <button @click="openModalUserLock()" class="btn btn-danger btn-sm">
                        <i class="fas fa-plus-square"> Bloquear</i>
                    </button>
                </div>
            </div>

            <div v-if="blocked_users.length" class="card-body">
                <div class="card-body table-responsive">
                    <table class="table table-hover table-head-fixed text-nowrap projects">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Foto</th>
                                <th>Correo</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in blocked_users" :key="user.id">
                                <td><b>{{ index+1 }}</b></td>
                                <td>
                                    <template>
                                        <div class="user-block">
                                            <img v-if="user.urlProfilePicture" :src="user.urlProfilePicture" :alt="user.username" class="profile-avatar-img img-fluid img-circle">
                                            <img v-else src="/img/user-default.png" :alt="user.username" class="profile-avatar-img img-fluid img-circle">
                                        </div>
                                    </template>
                                </td>
                                <td v-text="user.email"></td>
                                <td v-text="user.fullName"></td>
                                <td>
                                    <el-tooltip class="item" effect="dark" content="Desbloquear" placement="bottom">
                                        <button @click="userLockAndUnlock('unlock', user.id)" class="btn btn-flat btn-success btn-xs">
                                            <i class="fas fa-lock-open"></i>
                                        </button>
                                    </el-tooltip>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-else class="alert alert-warning mx-2 text-center" style="margin-top: 18px;">
                No hay ningún elemento para mostrar
            </div>

            <div class="modal fade" id="modalUserLock" tabindex="-1" role="dialog" aria-hidden="true" >
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Bloquear Usuario</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <form class="needs-validation" v-on:submit.prevent="userLockAndUnlock('lock', formBlocked.user_id)">
                            <div class="modal-body">

                                <div class="form-row">
                                    <el-autocomplete
                                        class="inline-input col-12"
                                        v-model="search"
                                        :fetch-suggestions="querySearch"
                                        placeholder="Usuario"
                                        :trigger-on-focus="false"
                                        @select="handleSelect">
                                        <i
                                            class="el-icon-search el-input__icon"
                                            slot="suffix">
                                        </i>
                                    </el-autocomplete>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary" v-loading.fullscreen.lock="fullscreenLoading">Bloquear</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>



        </div>
</template>

<script>


export default {

    created() {
        this.getBlockedUsers();
    },
    data() {
        return {
            search: '',
            users_not_blocked: [],
            blocked_users: [],
            formBlocked: {
                user_id: ''
            },


            fullscreenLoading: false
        }
    },
    methods: {
        getUsersNotBlocked() {
            const url = '/cmsapi/files/sendings/users-not-blocked';
            axios.get(url)
            .then(res => {
                this.users_not_blocked = res.data
            })
            .catch(err => {
                console.error(err);
            })
        },
        getBlockedUsers() {
            const url = '/cmsapi/files/sendings/blocked-users';

            axios.get(url)
            .then(res => {
                this.blocked_users = res.data;
                this.getUsersNotBlocked();
            })
            .catch(err => {
                console.error(err);
            })
        },
        querySearch(queryString, cb) {
            let users = this.users;
            let results = users;
            if(queryString) {
                results = users.filter((u) => {
                    return (u.value.toLowerCase().indexOf(queryString.toLowerCase()) !== -1);
                });
            }

            // call callback function to return suggestions
            cb(results);
        },
        handleSelect(item) {
            this.formBlocked.user_id = item.id
        },
        openModalUserLock() {
            this.getUsersNotBlocked();
            $('#modalUserLock').modal('show');
        },
        userLockAndUnlock(action, user_id) {
            this.fullscreenLoading = false;
            const url = '/cmsapi/files/sendings/lock-unlock/user'

            axios.post(url, {
                user_id: user_id,
                action: action
            })
            .then(res => {
                this.getBlockedUsers();
                this.fullscreenLoading = false;
                Swal.fire({
                    title: res.data.msg,
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#modalUserLock').modal('hide');
                this.formBlocked.user_id = '';
                this.search= '';
            })
            .catch(err => {
                this.fullscreenLoading = false;
                if(err.response.data.msg_error || err.response.data.message) {
                    Swal.fire({
                        title: 'Error!',
                        text: err.response.data.msg_error ?? err.response.data.message,
                        icon: "error",
                        showCloseButton: true,
                        closeButtonColor: 'red',
                    });
                }
            })
        }
    },
    computed: {
        users() {
            if(this.users_not_blocked.length) {
                return this.users_not_blocked.map(user => {
                    return {
                        value: `${user.email}, ${user.fullName}`,
                        id: user.id
                    };
                });
            }

            return [];
        },
    },

}
</script>
