<template>
    <div class="card">
        <!-- Carga de datos -->
        <div v-if="!loaded" class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

        <div class="card-header">
            <div class="card-tools">
                <button v-if="authUserPermissions.includes('roles.store')" @click="openModalAddEdit('add')"
                    class="btn btn-info btn-sm">
                    <i class="fas fa-plus-square"> Nuevo Rol</i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="container-fluid">

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Criterio de Busqueda</h3>
                    </div>

                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label class="control-label">Nombre</label>
                                <input v-model="searches.name" type="text" class="form-control" name="name" placeholder="Nombre">
                            </div>
                            <!-- <div class="form-group col-9 col-sm-6 col-md-6 ">
                                <label class="control-label">Url Amigable</label>
                                <input v-model="searches.url" type="text" class="form-control" name="url" placeholder="Url Amigable">
                            </div> -->
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
                            <span v-show="roles.total" class="right badge badge-dark">{{ roles.total }}</span>
                        </h3>
                    </div>

                    <div v-if="roles.data.length" class="card-body">
                        <div class="card-body table-responsive">
                            <table class="table table-hover table-head-fixed text-nowrap projects">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(role, index) in roles.data" :key="role.id">
                                        <td>{{ role.name }}</td>
                                        <td>
                                            <button v-if="authUserPermissions.includes('roles.show')" @click="openModalAddEdit('show', role)"
                                                class="btn btn-flat btn-primary btn-xs" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button v-if="authUserPermissions.includes('roles.update')" @click="openModalAddEdit('edit', role)"
                                                    class="btn btn-flat btn-info btn-xs" title="Editar">
                                                    <i class="fas fa-pencil-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <pagination :class="'mt-2'" :align="'center'" :limit="5" :data="roles" @pagination-change-page="getRoles"></pagination>
                        </div>
                    </div>
                    <div v-else class="alert alert-warning mx-2 text-center" style="margin-top: 18px;">
                        No hay ningún elemento para mostrar
                    </div>
                </div>

                <!-- Modal -->
                <role-form-add-edit ref="roleFormAddEdit" @updateRoleList="updateRoleList"></role-form-add-edit>

            </div>

        </div>

    </div>
</template>

<script>
import RoleFormAddEdit from './RoleFormAddEditComponent';

export default {
    components: {RoleFormAddEdit},
    created() {
        this.getRoles();
    },
    watch: {
        'searches.name': function (newValue, oldValue) {
            this.getRoles();
        }
    },
    data() {
        return {
            roles: {data:[]},
            searches: {
                name: ''
            },

            loaded: false
        }
    },
    methods: {
        getRoles(page = 1) {
            this.loaded = false;
            const url = `/cmsapi/administration/roles?page=${page}`;
            axios.get(url, {
                params: this.searches
            }).then(res => {
                this.roles = res.data;
                this.loaded = true;
            })
        },
        clearSearches() {
            this.searches = {
                name: '',
            };
        },
        openModalAddEdit(action, role = null) {
            this.$refs.roleFormAddEdit.showForm(action, role);
        },
        updateRoleList(action = null) {
            this.getRoles(this.roles.current_page ?? 1 );
        },
    },
    computed: {
        authUserPermissions() {
            return JSON.parse(sessionStorage.getItem('listPermissionsByAuthUser'));
        }
    }
}
</script>
