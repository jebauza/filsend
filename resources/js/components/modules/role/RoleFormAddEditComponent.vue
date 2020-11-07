<template>
    <div class="modal fade" id="modalRoleFormAddEdit" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 v-if="modalType=='add'" class="modal-title">Nuevo Rol</h4>
                    <h4 v-else-if="modalType=='edit'" class="modal-title">Editar Rol</h4>
                    <h4 v-else class="modal-title">Ver Rol</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="needs-validation" v-on:submit.prevent="actionStoreUpdate">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">Rol</h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="form-group col-12">
                                                    <label for="name" :class="['control-label', errors.name ? 'text-danger' : '']">Nombre</label>
                                                    <input v-model="form.name" type="text" :class="['form-control', errors.name ? 'is-invalid' : '']" name="name" placeholder="Nombre" required :disabled="modalType=='show'">
                                                    <small v-if="errors.name" class="form-control-feedback text-danger">
                                                        {{ errors.name[0] }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">Lista de premisos</h3>
                                        </div>

                                        <div v-if="permissions.length" class="card-body row">
                                            <div v-if="errors.permissions" class="col-12 alert alert-danger alert-dismissible">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <h6><i class="icon fas fa-ban"></i> {{ errors.permissions[0] }}</h6>
                                            </div>
                                            <div v-for="(p, index) in filterPermissions" :key="p.id" class="col-12 col-lg-6 col-xl-4">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" :id="'checkboxPermission-'+(index)" v-model="p.checked" :disabled="modalType=='show'">
                                                        <label :for="'checkboxPermission-'+(index)" class="custom-control-label" style="cursor: pointer">{{ p.display_name }}</label>
                                                    </div>
                                                    <!-- <div class="custom-control custom-checkbox">
                                                        <input class="custom-control-input" type="checkbox" id="customCheckbox2" checked="">
                                                        <label for="customCheckbox2" class="custom-control-label">Custom Checkbox checked</label>
                                                    </div> -->
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else class="alert alert-warning mx-2 text-center" style="margin-top: 18px;">
                                            No hay ningún elemento para mostrar
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div v-show="modalType != 'show'" class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" v-loading.fullscreen.lock="fullscreenLoading">Guardar</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
</template>

<script>
export default {
    mounted() {
        this.getPermissions();
    },
    /* watch: {
        permissions: {
            handler: function (after, before) {

            }
        }
    }, */
    data() {
        return {
            modalType: 'add',
            form: {
                name: '',
                id: ''
            },
            errors: {},
            permissions: [],
            filterPermissions: [],

            fullscreenLoading: false
        }
    },
    methods: {
        showForm(action, role = null) {
            this.clearFilterPermissions()
            if(this.modalType != action) {
                this.clearForm();
            }
            this.modalType = action;
            if(this.modalType == 'edit' || this.modalType == 'show') {
                this.getPermissionsByRole(role);
                this.form = {
                    name: role.name,
                    id: role.id
                };
            }
            this.erros = {};
            $('#modalRoleFormAddEdit').modal('show');
        },
        clearFilterPermissions() {
            let me = this;
            me.filterPermissions = [];
            me.permissions.map((p, index) => {
                me.filterPermissions.push({
                    id: p.id,
                    name: p.name,
                    display_name: p.display_name,
                    checked: false
                });
            });
        },
        getPermissions() {
            const url = `/cmsapi/administration/permissions`;
            axios.get(url)
            .then(res => {
                this.permissions = res.data;
                this.clearFilterPermissions();
            })
            .catch(err => {
                console.error(err);
            })
        },
        getPermissionsByRole(role) {
            const url = `/cmsapi/administration/roles/${role.id}/permissions-by-role`;
            axios.get(url)
            .then(res => {
                const permissionsByRole = res.data;
                let me = this;
                me.filterPermissions = [];
                me.permissions.map((p, index) => {
                    let checked = permissionsByRole.find( pByRole => pByRole.id === p.id );
                    me.filterPermissions.push({
                        id: p.id,
                        name: p.name,
                        display_name: p.display_name,
                        checked: checked ? true : false
                    });
                });
            })
            .catch(err => {
                console.error(err);
            })
        },
        actionStoreUpdate() {
            this.fullscreenLoading = true;
            switch (this.modalType) {
                case 'add':
                    this.storeRole();
                    break;

                case 'edit':
                    this.updateRole();
                    break;
            }
        },
        storeRole() {
            //Obtengo los ids de los permisos asociado al rol
            let checkedIdPermissions = this.filterPermissions.filter(p_filter => p_filter.checked).map(p_map => p_map.id);
            const url = '/cmsapi/administration/roles/store';
            axios.post(url, {
                name: this.form.name,
                permissions: checkedIdPermissions
            })
            .then(res => {
                this.fullscreenLoading = false;
                Swal.fire({
                    title: res.data.msg,
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                });
                this.$emit('updateRoleList', 'add');
                $('#modalRoleFormAddEdit').modal('hide');
                this.clearForm();
            })
            .catch(err => {
                this.fullscreenLoading = false;
                if(err.response && err.response.status == 422) {
                    this.errors = err.response.data.errors;
                    //Buscar los errores de los elementos del array permissions
                    if(!this.errors.permissions && Object.keys(this.errors).length !== 0){
                        for (let i = 0; i < this.permissions.length; i++) {
                            if(this.errors.hasOwnProperty(`permissions.${i}`)){
                                this.errors.permissions = this.errors[`permissions.${i}`];
                                break;
                            }
                        }
                    }
                }else if(err.response.data.msg_error || err.response.data.message) {
                    Swal.fire({
                        title: 'Error!',
                        text: err.response.data.msg_error ?? err.response.data.message,
                        icon: "error",
                        showCloseButton: true,
                        closeButtonColor: 'red',
                    });
                }
            });
        },
        updateRole() {
            let checkedIdPermissions = this.filterPermissions.filter(p_filter => p_filter.checked).map(p_map => p_map.id);
            const url = `/cmsapi/administration/roles/${this.form.id}/update`;
            axios.put(url, {
                name: this.form.name,
                permissions: checkedIdPermissions
            }).then(res => {
                EventBus.$emit('verifyAuthenticatedUser', null);
                this.fullscreenLoading = false;
                Swal.fire({
                    title: res.data.msg,
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                });
                this.$emit('updateRoleList', 'add');
                $('#modalRoleFormAddEdit').modal('hide');
                this.clearForm();
            })
            .catch(err => {
                this.fullscreenLoading = false;
                if(err.response && err.response.status == 422) {
                    this.errors = err.response.data.errors;
                    //Buscar los errores de los elementos del array permissions
                    if(!this.errors.permissions && Object.keys(this.errors).length !== 0){
                        for (let i = 0; i < this.permissions.length; i++) {
                            if(this.errors.hasOwnProperty(`permissions.${i}`)){
                                this.errors.permissions = this.errors[`permissions.${i}`];
                                break;
                            }
                        }
                    }
                }else if(err.response.data.msg_error || err.response.data.message) {
                    Swal.fire({
                        title: 'Error!',
                        text: err.response.data.msg_error ?? err.response.data.message,
                        icon: "error",
                        showCloseButton: true,
                        closeButtonColor: 'red',
                    });
                }
            });
        },
        clearForm() {
            this.form = {
                name: '',
                id: ''
            };
            this.errors = {};
        }
    },
}
</script>

