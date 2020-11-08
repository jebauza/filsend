<template>
<div class="card">

    <!-- Carga de datos -->
    <div v-if="!loaded" class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

    <div v-if="user" class="card-body">
        <div class="row">
            <div class="col-md-4">

                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img v-if="user.urlProfilePicture" :src="user.urlProfilePicture" class="profile-user-img img-fluid img-circle img-max-height" :alt="user.username">
                            <img v-else class="profile-user-img img-fluid img-circle img-max-height" src="/img/avatar.png" :alt="user.username">
                        </div>

                        <h3 class="profile-username text-center" v-text="user.fullName"></h3>
                        <!-- <p class="text-muted text-center">Rol</p> -->
                    </div>
                </div>

                <!-- About Me Box -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Sobre Mi</h3>
                    </div>

                    <div class="card-body">
                        <strong><i class="fas fa-user-circle"></i> Nombre Completo</strong>
                        <p class="text-muted" v-text="user.fullName"></p>
                        <hr>
                        <strong><i class="fas fa-envelope-open-text"></i> Correo Electronico</strong>
                        <p class="text-muted" v-text="user.email"></p>
                    </div>
                </div>

            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Información</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="settings">
                                <form class="form-horizontal needs-validation" v-on:submit.prevent="updateUser">

                                    <div class="form-group row">
                                        <label for="firstName" :class="['col-sm-3', 'col-form-label', errors.firstname ? 'text-danger' : '']">Primer Nombre</label>
                                        <div class="col-sm-9">
                                            <input v-model="form.firstname" type="text" :class="['form-control', errors.firstname ? 'is-invalid' : '']" name="firstName" placeholder="Primer Nombre" required>
                                            <small v-if="errors.firstname" class="form-control-feedback text-danger">
                                                {{ errors.firstname[0] }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="secondName" :class="['col-sm-3', 'col-form-label', errors.secondname ? 'text-danger' : '']">Segundo Nombe</label>
                                        <div class="col-sm-9">
                                            <input v-model="form.secondname" type="text" :class="['form-control', errors.secondname ? 'is-invalid' : '']" name="secondName" placeholder="Segundo Nombe">
                                            <small v-if="errors.secondname" class="form-control-feedback text-danger">
                                                {{ errors.secondname[0] }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="lastName" :class="['col-sm-3', 'col-form-label', errors.lastname ? 'text-danger' : '']">Apellidos</label>
                                        <div class="col-sm-9">
                                            <input v-model="form.lastname" type="text" :class="['form-control', errors.lastname ? 'is-invalid' : '']" name="lastName" placeholder="Apellidos" required>
                                            <small v-if="errors.lastname" class="form-control-feedback text-danger">
                                                {{ errors.lastname[0] }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="userName" :class="['col-sm-3', 'col-form-label', errors.username ? 'text-danger' : '']">Usuario</label>
                                        <div class="col-sm-9">
                                            <input v-model="form.username" type="text" :class="['form-control', errors.username ? 'is-invalid' : '']" name="userName" placeholder="Usuario" required>
                                            <small v-if="errors.username" class="form-control-feedback text-danger">
                                                {{ errors.username[0] }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="email" :class="['col-sm-3', 'col-form-label', errors.email ? 'text-danger' : '']">Correo</label>
                                        <div class="col-sm-9">
                                            <input v-model="form.email" type="email" :class="['form-control', errors.email ? 'is-invalid' : '']" name="email" placeholder="Correo" required>
                                            <small v-if="errors.email" class="form-control-feedback text-danger">
                                                {{ errors.email[0] }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="password" :class="['col-sm-3', 'col-form-label', errors.password ? 'text-danger' : '']">Contraseña</label>
                                        <div class="col-sm-9">
                                            <input v-model="form.password" type="email" :class="['form-control', errors.password ? 'is-invalid' : '']" name="password" placeholder="Contraseña">
                                            <small v-if="errors.password" class="form-control-feedback text-danger">
                                                {{ errors.password[0] }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
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

                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-flat btn-info btnFull" v-loading.fullscreen.lock="fullscreenLoading">Guardar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                </div>

            </div>


        </div>
    </div>
</div>
</template>

<script>
export default {
    mounted() {
        this.getUserById();
    },
    data() {
        return {
            user: '',
            form: {
                firstname: '',
                secondname: '',
                lastname: '',
                username: '',
                email: '',
                password: '',
                image: '',
                id: ''
            },
            errors: {},

            fullscreenLoading: false,
            loaded: false
        }
    },
    methods: {
        getUserById() {
            this.loaded = false;
            const url = `/cmsapi/administration/users/${this.$attrs.id}/show`;
            axios.get(url)
            .then(res => {
                this.user = res.data;
                this.form = {
                    firstname: this.user.firstname,
                    secondname: this.user.secondname,
                    lastname: this.user.lastname,
                    username: this.user.username,
                    email: this.user.email,
                    password: '',
                    image: '',
                    id: this.user.id
                };
                this.loaded = true;
            })
            .catch(err => {
                console.error(err);
                this.loaded = true;
            })
        },
        getFile(e) {
            this.form.image = e.target.files[0];
        },
        updateUser() {
            this.fullscreenLoading = true;
            const config = { headers: { 'content-type': 'multipart/form-data' } };
            let formData = new FormData;
            for (const property in this.form) {
                if(property !== 'id') {
                    formData.append(property, this.form[property]);
                }
            }

            const url = `/cmsapi/administration/users/${this.form.id}/update`;
            axios.post(url, formData, config)
            .then(res => {
                this.user = res.data.user;
                this.refreshUserAuth();
                this.form = {
                    firstname: this.user.firstname,
                    secondname: this.user.secondname,
                    lastname: this.user.lastname,
                    username: this.user.username,
                    email: this.user.email,
                    password: '',
                    image: '',
                    id: this.user.id
                };
                this.fullscreenLoading = false;
                Swal.fire({
                    title: res.data.msg,
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                });

            })
            .catch(err => {
                this.fullscreenLoading = false;
                if(err.response && err.response.status == 422) {
                    this.errors = err.response.data.errors;
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
        refreshUserAuth() {
            const url = '/cmsapi/auth/get-refresh-auth-user';
            axios.get(url)
            .then(res => {
                let authUser = res.data.authUser;
                EventBus.$emit('verifyAuthenticatedUser', authUser);
            });
        }
    },
}
</script>

<style scope>
    .img-max-height{
        max-block-size: 100px !important;
    }
</style>
