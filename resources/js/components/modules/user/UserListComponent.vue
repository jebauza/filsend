<template>
    <div class="card">
        <!-- Carga de datos -->
        <div v-if="!loaded" class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

        <div class="card-header">
            <div v-if="authUserPermissions.includes('users.store')" class="card-tools">
                <button @click="openModalAddEdit('add')" class="btn btn-info btn-sm">
                    <i class="fas fa-plus-square"> Nuevo Usuario</i>
                </button>
            </div>
        </div>

        <div class="card-body" v-loading.fullscreen.lock="fullscreenLoading">
            <div class="container-fluid">

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Criterio de Busqueda</h3>
                    </div>

                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                <label class="control-label">Nombre</label>
                                <input v-model="searches.name" type="text" class="form-control" name="name" placeholder="Nombre">
                            </div>
                            <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                <label class="control-label">Usuario</label>
                                <input v-model="searches.username" type="text" class="form-control" name="username" placeholder="Usuario">
                            </div>
                            <div class="form-group col-sm-6 col-md-4 col-lg-3">
                                <label class="control-label">Correo</label>
                                <input v-model="searches.email" type="text" class="form-control" name="email" placeholder="Correo">
                            </div>
                            <div class="form-group col-sm-4 col-md-4 col-lg-2">
                                <label class="control-label">Estado</label>
                                <select v-model="searches.state" class="form-control custom-select" name="state">
                                    <option value="" selected></option>
                                    <option value="A">Activo</option>
                                    <option value="I">Inhabilitado</option>
                                </select>
                            </div>
                            <div class="form-group col-auto mt-4 pt-2">
                                <button @click="clearSearches()" title="Eliminar Filtros" type="button"
                                    class="btn waves-effect waves-light btn-danger float-right">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Bandeja de Resultados
                            <span v-show="users.total" class="right badge badge-dark">{{ users.total }}</span>
                        </h3>
                    </div>

                    <div v-if="users.data.length" class="card-body">
                        <div class="card-body table-responsive">
                            <table class="table table-hover table-head-fixed text-nowrap projects">
                                <thead>
                                    <tr>
                                        <th>Foto</th>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Usuario</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, index) in users.data" :key="user.id" :class="user.state == 'I' ? 'table-danger' : ''">
                                        <td>
                                            <template>
                                                <div class="user-block">
                                                    <img v-if="user.urlProfilePicture" :src="user.urlProfilePicture" :alt="user.username" class="profile-avatar-img img-fluid img-circle">
                                                    <img v-else src="/img/avatar.png" :alt="user.username" class="profile-avatar-img img-fluid img-circle">
                                                </div>
                                            </template>
                                        </td>
                                        <td v-text="user.fullName"></td>
                                        <td v-text="user.email"></td>
                                        <td v-text="user.username"></td>
                                        <td>
                                            <el-tooltip v-if="user.state == 'A'" class="item" effect="dark" content="Activo" placement="bottom">
                                                <span class="badge badge-success"><i class="fas fa-user-check"></i></span>
                                            </el-tooltip>
                                            <el-tooltip v-else class="item" effect="dark" content="Inactivo" placement="bottom">
                                                <span class="badge badge-danger"><i class="fas fa-user-lock"></i></span>
                                            </el-tooltip>
                                        </td>
                                        <td>
                                            <el-tooltip class="item" effect="dark" content="Ver Perfil" placement="bottom">
                                                <router-link :to="{name: 'profile', params: {id: user.id}}" class="btn btn-flat btn-primary btn-xs">
                                                    <i class="fas fa-eye"></i>
                                                </router-link>
                                            </el-tooltip>
                                            <template v-if="user.state == 'A'">
                                                <el-tooltip class="item" effect="dark" content="Editar" placement="bottom">
                                                    <button v-if="authUserPermissions.includes('users.update')" @click="openModalAddEdit('edit', user)"
                                                        class="btn btn-flat btn-info btn-xs">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                </el-tooltip>
                                                <el-tooltip class="item" effect="dark" content="Inhabilitar" placement="bottom">
                                                    <button v-if="authUserPermissions.includes('users.deactivate')" @click="setUserState('I', user)"
                                                        class="btn btn-flat btn-danger btn-xs">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                </el-tooltip>
                                            </template>
                                            <template v-else>
                                                <el-tooltip class="item" effect="dark" content="Habilitar" placement="bottom">
                                                    <button v-if="authUserPermissions.includes('users.activate')" @click="setUserState('A', user)"
                                                        class="btn btn-flat btn-success btn-xs">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                </el-tooltip>
                                            </template>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <pagination :class="'mt-2'" :align="'center'" :limit="5" :data="users" @pagination-change-page="getUsers"></pagination>
                        </div>
                    </div>
                    <div v-else class="alert alert-warning mx-2 text-center" style="margin-top: 18px;">
                        No hay ningún elemento para mostrar
                    </div>
                </div>

                <user-form-add-edit ref="userFormAddEdit" @updateUserList="updateUserList"></user-form-add-edit>

            </div>

        </div>

    </div>
</template>

<script>
import UserFormAddEdit from './UserFormAddEditComponent';

export default {
    components: {UserFormAddEdit},
    created() {
        this.getUsers();
    },
    watch: {
        'searches.name': function (newValue, oldValue) {
            this.getUsers();
        },
        'searches.username': function (newValue, oldValue) {
            this.getUsers();
        },
        'searches.email': function (newValue, oldValue) {
            this.getUsers();
        },
        'searches.state': function (newValue, oldValue) {
            this.getUsers();
        }
    },
    data() {
        return {
            users: {data:[]},
            searches: {
                name: '',
                username: '',
                email: '',
                state: '',
            },

            fullscreenLoading: false,
            loaded: false
        }
    },
    methods: {
        getUsers(page = 1) {
            this.loaded = false;
            const url = `/cmsapi/administration/users?page=${page}`;
            axios.get(url, {
                params: this.searches
            }).then(res => {
                this.users = res.data;
                this.loaded = true;
            })
        },
        clearSearches() {
            this.searches = {
                name: '',
                username: '',
                email: '',
                state: '',
            };
        },
        openModalAddEdit(action, user = null) {
            this.$refs.userFormAddEdit.showForm(action, user);
        },
        updateUserList(action = null) {
            this.getUsers(this.users.current_page ?? 1 );
        },
        setUserState(newState, user) {
            Swal.fire({
                title: 'Estas seguro de ' + (newState == 'I' ? 'desactivar' : 'activar') + ' el usuario',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: (newState == 'I' ? 'Si, desactivar' : 'Si, activar')
            }).then((result) => {
                if (result.isConfirmed) {
                    this.fullscreenLoading = true;
                    const url = `/cmsapi/administration/users/${user.id}/set-state`;
                    axios.put(url,{
                        state: newState
                    })
                    .then(res => {
                        this.fullscreenLoading = false;
                        Swal.fire({
                            title: res.data.msg,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                        this.updateUserList();
                    })
                    .catch(err => {
                        this.fullscreenLoading = false;
                        if(err.response.data.msg_error || err.response.data.message)
                        {
                            Swal.fire({
                                title: 'Error!',
                                text: err.response.data.msg_error ? err.response.data.msg_error : err.response.data.message,
                                icon: "error",
                                showCloseButton: true,
                                closeButtonColor: 'red',
                            });
                        }
                        console.log(err.response.data);
                    })
                }
            });
        }
    },

    computed: {
        authUserPermissions() {
            return JSON.parse(sessionStorage.getItem('listPermissionsByAuthUser'));
        }
    },

}
</script>
