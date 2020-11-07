<template>
<aside class="main-sidebar elevation-4 sidebar-dark-primary">
        <!-- Brand Logo -->
        <router-link :to="{name:'home'}" class="brand-link navbar-danger">
            <img :src="basepath + '/img/AdminLTELogo.png'" alt="AdminLTE Logo"
                class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-bold">Filsend</span>
        </router-link>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user (optional) -->
            <div class="user-panel mt-3 pb-3 mb-1 d-flex">
                <div class="image">
                    <router-link :to="{name:'profile', params: {id: auth_user.id}}" class="d-block">
                        <img v-if="auth_user.urlProfilePicture" class="img-circle elevation-2" style="height:34px !important;" :src="auth_user.urlProfilePicture" :alt="auth_user.username">
                        <img v-else class="img-circle elevation-2" style="height:34px !important;" :src="basepath + '/img/avatar.png'" :alt="auth_user.username">
                    </router-link>
                </div>
                <div class="info">
                    <router-link :to="{name:'profile', params: {id: auth_user.id}}" class="d-block">
                        {{ `${auth_user.firstname} ${auth_user.secondname ? auth_user.secondname : ''}`}}
                    </router-link>
                </div>
            </div>

            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-compact user-panel mb-3"
                data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="#" class="nav-link" @click.prevent="logout" v-loading.fullscreen.lock="fullscreenLoading">
                        <i class="fas fa-sign-out-alt"></i>
                        <p> Cerrar Sección</p>
                    </a>
                </li>
            </ul>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-compact"
                    data-widget="treeview" role="menu" data-accordion="false">

                    <li class="nav-item has-treeview menu-open">
                        <router-link :to="{path: '/home'}" :class="['nav-link', isActive('/home') ? 'active' : '']">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p> Dashboard</p>
                        </router-link>
                    </li>

                    <!-- ADMINISTRACION -->
                    <template v-if="userPermissions.find(p => p === 'users.index' || p === 'roles.index')">
                        <li class="nav-header">ADMINISTRACION</li>
                        <li v-if="userPermissions.includes('users.index')" class="nav-item">
                            <router-link :to="{path: '/users'}" :class="['nav-link', isActive('/users') ? 'active' : '']">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Usuarios</p>
                            </router-link>
                        </li>
                        <li v-if="userPermissions.includes('roles.index')" class="nav-item">
                            <router-link :to="{path: '/roles'}" :class="['nav-link', isActive('/roles') ? 'active' : '']">
                                <i class="nav-icon fas fa-unlock-alt"></i>
                                <p>Roles</p>
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-key"></i>
                                <p>Permisos</p>
                            </a>
                        </li>
                    </template>


                    <li class="nav-header">REPORTES</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-export"></i>
                            <p>Pedidos</p>
                        </a>
                    </li>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script>
export default {
    props: ['basepath', 'auth_user', 'userPermissions'],
    data() {
        return {
            fullscreenLoading: false
        }
    },
    methods: {
        isActive(path_url) {
            return this.currentPage.indexOf(path_url) === 0;
        },
        logout() {
            this.fullscreenLoading = true;
            const url = '/cmsapi/auth/logout';
            axios.get(url)
            .then(res => {
                window.location.href = '/login';
                //this.fullscreenLoading = false;
                /* this.$router.push({name: 'login'});
                location.reload(); */
            });
        }
    },
    computed: {
        currentPage() {
            return this.$route.path;
        }
    }
}
</script>

<style>

</style>
