<template>

    <nav class="main-header navbar navbar-expand navbar-dark navbar-gray">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <router-link :to="{name: 'home'}" class="nav-link">Home</router-link>
            </li>
        </ul>

        <!-- SEARCH FORM -->
        <form class="form-inline ml-3">
            <div class="input-group input-group-sm">
                <el-autocomplete
                    class="inline-input"
                    v-model="search"
                    :fetch-suggestions="querySearch"
                    placeholder="Buscar..."
                    :trigger-on-focus="false"
                    size="small"
                    @select="handleSelect">
                    <i
                        class="el-icon-search el-input__icon"
                        slot="suffix">
                    </i>
                </el-autocomplete>
            </div>
        </form>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            
            <li class="nav-item dropdown ml-3">
                <a class="user-profile dropdown-toggle text-white" data-toggle="dropdown" href="#">
                    <img v-if="auth_user.urlProfilePicture" :src="auth_user.urlProfilePicture" class="rounded-circle" width="40" height="40" :alt="auth_user.username">
                    <img v-else :src="basepath + '/img/user-default.png'" class="rounded-circle" alt="Cinque Terre" width="40" height="40">
                </a>
                <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                    <router-link :to="{name:'profile', params: {id: auth_user.id}}" class="dropdown-item" >
                        <i class="fas fa-address-card"></i> Perfil
                    </router-link>

                    <a href="#" class="dropdown-item"
                        @click.prevent="logout" v-loading.fullscreen.lock="fullscreenLoading">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>

                </div>
            </li>
        </ul>
    </nav>

</template>

<script>
export default {
    props: ['basepath', 'auth_user', 'userPermissions'],
    mounted() {
        EventBus.$on('verifyAuthenticatedUser', user => {
            this.getListPermissionsByAuthUser();
        });
        this.getListPermissionsByAuthUser();
    },
    data() {
        return {
            search: '',
            links: [],
            fullscreenLoading: false
        }
    },
    methods: {
        querySearch(queryString, cb) {
            let links = this.links;
            let results = links;
            if(queryString) {
                results = links.filter((link) => {
                    return (link.value.toLowerCase().indexOf(queryString.toLowerCase()) !== -1);
                });
            }

            // call callback function to return suggestions
            cb(results);
        },
        handleSelect(item) {
            console.log(item);
            if(this.$route.name != item.link) {
                this.$router.push({name: item.link});
                this.search = '';
            }else {
                this.search = '';
            }
        },
        getListPermissionsByAuthUser() {
            this.links = [];
            const url = '/cmsapi/administration/permissions/auth-user/get-all-permissions';

            axios.get(url)
            .then(res => {
                res.data.map(permission => {
                    if(permission.name.includes('index')) {
                        this.links.push({
                            value: permission.display_name,
                            link: permission.name.split('.')[0]
                        });
                    }
                });

            });
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
    }
}
</script>

<style>

</style>
