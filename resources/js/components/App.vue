<template>
<div>
    <Navbar :basepath="basepath" :auth_user="authUser" :userPermissions="userPermissions"></Navbar>

    <Sidebar :basepath="basepath" :auth_user="authUser" :userPermissions="userPermissions"></Sidebar>

    <Content :basepath="basepath"></Content>

    <Footer :basepath="basepath"></Footer>

</div>
</template>

<script>
import Navbar from './layouts/Navbar';
import Sidebar from './layouts/Sidebar';
import Content from './layouts/Content';
import Footer from './layouts/Footer';

export default {
    components: {Navbar,Sidebar,Content,Footer},
    props: ['basepath', 'auth_user'],
    mounted() {
        sessionStorage.setItem('authUser', JSON.stringify(this.auth_user));
        this.userPermissions = JSON.parse(sessionStorage.getItem('listPermissionsByAuthUser'));

        EventBus.$on('verifyAuthenticatedUser', user => {
            const user_id = user ? user.id : this.authUser.id;
            if(user_id === this.authUser.id) {
                this.authUser = user ? user : this.authUser;
                this.refreshUserAuth();
            }
        });

        this.refreshUserAuth();
    },
    data() {
        return {
            authUser: this.auth_user,
            userPermissions: [],
        }
    },
    methods: {
        refreshUserAuth() {
            const url = '/cmsapi/auth/get-refresh-auth-user';
            axios.get(url)
            .then(res => {
                this.authUser = res.data.authUser;
                this.userPermissions = res.data.userPermissions;
                sessionStorage.setItem('authUser', JSON.stringify(this.auth_user));
                sessionStorage.setItem('listPermissionsByAuthUser', JSON.stringify(this.userPermissions));
            });
        }
    },
}
</script>
