<template>
    <div class="modal fade" id="modalUserFormAddEdit" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 v-if="modalType=='add'" class="modal-title">Nuevo Usuario</h4>
                    <h4 v-else class="modal-title">Editar Usuario</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="needs-validation" v-on:submit.prevent="actionStoreUpdate">
                    <div class="modal-body">

                        <div class="form-row">
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="firstName" :class="['control-label', errors.firstname ? 'text-danger' : '']">Primer Nombre</label>
                                <input v-model="form.firstname" type="text" :class="['form-control', errors.firstname ? 'is-invalid' : '']" name="firstName" placeholder="Primer Nombre" required>
                                <small v-if="errors.firstname" class="form-control-feedback text-danger">
                                    {{ errors.firstname[0] }}
                                </small>
                            </div>
                            <div class="form-group col-md-6 col-lg-3">
                                <label for="secondName" :class="['control-label', errors.secondname ? 'text-danger' : '']">Segundo Nombe</label>
                                <input v-model="form.secondname" type="text" :class="['form-control', errors.secondname ? 'is-invalid' : '']" name="secondName" placeholder="Segundo Nombe">
                                <small v-if="errors.secondname" class="form-control-feedback text-danger">
                                    {{ errors.secondname[0] }}
                                </small>
                            </div>
                            <div class="form-group col-md-6 col-lg-5">
                                <label for="lastName" :class="['control-label', errors.lastname ? 'text-danger' : '']">Apellidos</label>
                                <input v-model="form.lastname" type="text" :class="['form-control', errors.lastname ? 'is-invalid' : '']" name="lastName" placeholder="Apellidos" required>
                                <small v-if="errors.lastname" class="form-control-feedback text-danger">
                                    {{ errors.lastname[0] }}
                                </small>
                            </div>
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="userName" :class="['control-label', errors.username ? 'text-danger' : '']">Usuario</label>
                                <input v-model="form.username" type="text" :class="['form-control', errors.username ? 'is-invalid' : '']" name="userName" placeholder="Usuario" required>
                                <small v-if="errors.username" class="form-control-feedback text-danger">
                                    {{ errors.username[0] }}
                                </small>
                            </div>
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="email" :class="['control-label', errors.email ? 'text-danger' : '']">Correo</label>
                                <input v-model="form.email" type="email" :class="['form-control', errors.email ? 'is-invalid' : '']" name="email" placeholder="correo" required>
                                <small v-if="errors.email" class="form-control-feedback text-danger">
                                    {{ errors.email[0] }}
                                </small>
                            </div>
                            <div class="form-group col-md-6 col-lg-4">
                                <label for="password" :class="['control-label', errors.password ? 'text-danger' : '']">Contraseña</label>
                                <input v-model="form.password" type="password" :class="['form-control', errors.password ? 'is-invalid' : '']" name="password" placeholder="Contraseña" :required="modalType=='add'">
                                <small v-if="errors.password" class="form-control-feedback text-danger">
                                    {{ errors.password[0] }}
                                </small>
                            </div>

                            <div class="col-12">
                                <div class="custom-file">
                                    <input type="file" @change="getFile" :class="['custom-file-input', errors.image ? 'is-invalid' : '']" id="customFileLangHTML">
                                    <label :class="['custom-file-label', errors.image ? 'text-danger' : '']" for="customFileLangHTML" data-browse="Elegir Foto">
                                        {{ (form.image ? form.image.name : 'Selecione Foto') }}
                                    </label>
                                    <small v-if="errors.image" class="form-control-feedback text-danger">
                                        {{ errors.image[0] }}
                                    </small>
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Roles y permisos</h3>

                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                                            </button>
                                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i>
                                            </button> -->
                                        </div>
                                    </div>

                                    <div class="card-body" style="display: block;">
                                        <div class="form-row">
                                            <div class="col-md-6 form-group">
                                                <label for="roles" :class="['control-label', errors.roles ? 'text-danger' : '']">Roles</label>
                                                <!-- <input type="text" :class="['form-control', errors.roles ? 'is-invalid' : '']" name="roles" placeholder="Roles"> -->
                                                <multiselect
                                                    v-model="form.roles"
                                                    :options="allRoles"
                                                    placeholder="Roles"
                                                    label="name"
                                                    track-by="id"
                                                    :multiple="true"
                                                    :close-on-select="false"
                                                    :clear-on-select="false"
                                                    :preserve-search="true"
                                                    >
                                                </multiselect>
                                                <small v-if="errors.roles" class="form-control-feedback text-danger">
                                                    {{ errors.roles[0] }}
                                                </small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="permissions" :class="['control-label', errors.permissions ? 'text-danger' : '']">Permisos</label>
                                                <multiselect
                                                    v-model="form.permissions"
                                                    :options="allPermissions"
                                                    placeholder="Permisos"
                                                    label="display_name"
                                                    track-by="id"
                                                    :multiple="true"
                                                    :close-on-select="false"
                                                    :clear-on-select="false"
                                                    :preserve-search="true">
                                                </multiselect>
                                                <small v-if="errors.permissions" class="form-control-feedback text-danger">
                                                    {{ errors.permissions[0] }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
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
import Multiselect from 'vue-multiselect';

export default {
    components: {Multiselect},
    mounted() {
        this.getAllRoles();
        this.getAllPermissions();
    },
    data() {
        return {
            modalType: '', //add, edit
            allRoles: [],
            allPermissions: [],
            form: {
                firstname: '',
                secondname: '',
                lastname: '',
                username: '',
                email: '',
                password: '',
                image: '',
                roles: [],
                permissions: [],
                id: ''
            },
            errors: {},

            fullscreenLoading: false
        }
    },
    methods: {
        showForm(action, user = null) {
            if(this.modalType != action) {
                this.clearForm();
            }
            this.modalType = action;
            if(this.modalType === 'edit' && user) {
                this.form = {
                    firstname: user.firstname,
                    secondname: user.secondname,
                    lastname: user.lastname,
                    username: user.username,
                    email: user.email,
                    password: '',
                    image: '',
                    roles: user.roles,
                    permissions: user.permissions,
                    id: user.id
                }
            }
            this.erros = {};
            $('#modalUserFormAddEdit').modal('show');
        },
        getFile(e) {
            this.form.image = e.target.files[0];
        },
        actionStoreUpdate() {
            this.fullscreenLoading = true;
            switch (this.modalType) {
                case 'add':
                    this.storeUser();
                    break;

                case 'edit':
                    this.updateUser();
                    break;
            }
        },
        storeUser() {
            const config = { headers: { 'content-type': 'multipart/form-data' } };
            let formData = new FormData;
            for (const property in this.form) {
                if(property !== 'id') {
                    if(property === 'roles' || property === 'permissions'){
                        let arr_ids = this.form[property].map(item => item.id);
                        for (let i = 0; i < arr_ids.length; i++) {
                            formData.append(property+'[]', arr_ids[i]);
                        }
                    }else{
                        formData.append(property, this.form[property]);
                    }
                }
            }

            const url = '/cmsapi/administration/users/store';
            axios.post(url, formData, config)
            .then(res => {
                this.fullscreenLoading = false;
                Swal.fire({
                    title: res.data.msg,
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                });
                this.$emit('updateUserList', 'add');
                $('#modalUserFormAddEdit').modal('hide');
                this.clearForm();
            })
            .catch(err => {
                this.fullscreenLoading = false;
                if(err.response && err.response.status == 422) {
                    this.errors = err.response.data.errors;
                    this.errorsRolesPermissions();
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
        updateUser() {
            const config = { headers: { 'content-type': 'multipart/form-data' } };
            let formData = new FormData;
            for (const property in this.form) {
                if(property !== 'id') {
                    if(property === 'roles' || property === 'permissions'){
                        let arr_ids = this.form[property].map(item => item.id);
                        for (let i = 0; i < arr_ids.length; i++) {
                            formData.append(property+'[]', arr_ids[i]);
                        }
                    }else{
                        formData.append(property, this.form[property]);
                    }
                }
            }

            const url = `/cmsapi/administration/users/${this.form.id}/update`;
            axios.post(url, formData, config)
            .then(res => {
                EventBus.$emit('verifyAuthenticatedUser', res.data.user);
                this.fullscreenLoading = false;
                Swal.fire({
                    title: res.data.msg,
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                });
                this.$emit('updateUserList', 'edit');
                $('#modalUserFormAddEdit').modal('hide');
                this.clearForm();
            })
            .catch(err => {
                this.fullscreenLoading = false;
                if(err.response && err.response.status == 422) {
                    this.errors = err.response.data.errors;
                    this.errorsRolesPermissions();
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
                firstname: '',
                secondname: '',
                lastname: '',
                username: '',
                email: '',
                password: '',
                image: '',
                roles: [],
                permissions: [],
                id: ''
            };
            this.errors = {};
        },
        getAllRoles() {
            const url = '/cmsapi/administration/roles/get-all-roles';
            axios.get(url)
            .then(res => {
                this.allRoles = res.data;
            })
            .catch(err => {
                console.error(err);
            })
        },
        getAllPermissions() {
            const url = '/cmsapi/administration/permissions/get-all-permissions';
            axios.get(url)
            .then(res => {
                this.allPermissions = res.data;
            })
            .catch(err => {
                console.error(err);
            })
        },
        errorsRolesPermissions() {
            //Buscar los errores de los elementos del array permissions
            if(Object.keys(this.errors).length !== 0){
                if(!this.errors.roles){
                    for (let i = 0; i < this.allRoles.length; i++) {
                        if(this.errors.hasOwnProperty(`roles.${i}`)){
                            this.errors.roles = this.errors[`roles.${i}`];
                            break;
                        }
                    }
                }
                if(!this.errors.permissions){
                    for (let i = 0; i < this.allPermissions.length; i++) {
                        if(this.errors.hasOwnProperty(`permissions.${i}`)){
                            this.errors.permissions = this.errors[`permissions.${i}`];
                            break;
                        }
                    }
                }
            }
        }
    },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

