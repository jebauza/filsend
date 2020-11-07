import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

function accessVerification(to, from, next, permission) {
    const authUser = JSON.parse(sessionStorage.getItem('authUser'));
    if(authUser) {
        const userPermissions = JSON.parse(sessionStorage.getItem('listPermissionsByAuthUser'));
        if(userPermissions.includes(permission)) {
            next();
        }else {
            next(from.path != '/' ? from.path : '/home');
        }
    }
}

export const routes = [
    {
        path: '/login',
        name: 'login',
        component: require('./components/modules/authenticate/LoginComponent').default,
        /* beforeEnter: (to, from, next) => {
            let authUser = JSON.parse(sessionStorage.getItem('authUser'));
            if(authUser) {
                next({name: 'home'});
            }else {
                next();
            }
        } */
    },
    {
        path: '/home',
        name: 'home',
        component: require('./components/dashboard/HomeComponent').default,
        meta: {
            breadcrumb: [
                { name: 'Home' }
            ]
        }
    },
    {
        path: '/users',
        name: 'users',
        component: require('./components/modules/user/UserListComponent').default,
        beforeEnter: (to, from, next) => {
            accessVerification(to, from, next, 'users.index');
        },
        meta: {
            breadcrumb: [
                { name: 'Home', link: '/home' },
                { name: 'Usuarios' }
            ]
        }
    },
    {
        path: '/users/:id/profile/',
        name: 'profile',
        component: require('./components/modules/user/UserProfileComponent').default,
        props: true,
        meta: {
            breadcrumb: [
                { name: 'Home', link: '/home' },
                { name: 'Usuarios', link: '/users' },
                { name: 'Perfil' }
            ]
        }
    },
    {
        path: '/roles',
        name: 'roles',
        component: require('./components/modules/role/RoleListComponent.vue').default,
        /* middleware */
        beforeEnter: (to, from, next) => {
            accessVerification(to, from, next, 'roles.index');
        },
        meta: {
            breadcrumb: [
                { name: 'Home', link: '/home' },
                { name: 'Roles' }
            ]
        }
    },



    {
        path: '*',
        component: require('./components/layouts/404').default,
        meta: {
            breadcrumb: [
                { name: 'Home', link: '/home' },
                { name: '404' }
            ]
        }
    },
];

export default new Router({
    routes: routes,
    mode: 'history'
});
