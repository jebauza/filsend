<template>

    <div class="login-box">
        <div class="login-logo">
            <router-link :to="{name:'login'}">
                <b>Iniciar Sección</b>
            </router-link>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Ingrese sus credenciales</p>

                <form @submit.prevent="login">
                    <div class="input-group mb-3">
                        <input @keyup.enter="login" type="email" placeholder="Correo"
                            v-model="form.email" name="email"
                            :class="['form-control', errors.email ? 'is-invalid' : '']"
                            required autocomplete="email" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <span v-if="errors.email" class="invalid-feedback" role="alert">
                            <strong>{{ errors.email[0] }}</strong>
                        </span>
                    </div>
                    <div class="input-group mb-3">
                        <input @keyup.enter="login" type="password" placeholder="Contraseña"
                            v-model="form.password" name="password"
                            :class="['form-control', errors.password ? 'is-invalid' : '']"
                            required autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <span v-if="errors.password" class="invalid-feedback" role="alert">
                            <strong>{{ errors.password[0] }}</strong>
                        </span>
                    </div>

                    <div class="social-auth-links text-center mb-3">
                        <button type="submit" class="btn btn-flat btn-block btn-primary" v-loading.fullscreen.lock="fullscreenLoading">Login</button>
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>

    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                email: '',
                password: ''
            },
            errors: {},

            fullscreenLoading: false
        }
    },
    methods: {
        login() {
            this.fullscreenLoading = true;
            const url = '/cmsapi/auth/login';
            axios.post(url,this.form)
            .then(res => {
                //window.location.href = '/home';
                //this.fullscreenLoading = false;
                sessionStorage.setItem('authUser', JSON.stringify(res.data.authUser));
                sessionStorage.setItem('listPermissionsByAuthUser', JSON.stringify(res.data.userPermissions.map(p => p.name)));
                location.reload();
            })
            .catch(err => {
                this.fullscreenLoading = false;
                this.errors = err.response.data.errors;
            })
        }
    },
}
</script>

<style>

</style>
