<template>
    <div class="card">
        <!-- Carga de datos -->
        <div v-if="!loaded" class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Enviados</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Recibidos</a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="#tab_3" data-toggle="tab">Usuarios Bloqueados</a></li> -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                        Usuarios <span class="caret"></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#tab_3" data-toggle="tab">Bloqueados</a>
                        <a class="dropdown-item" tabindex="-1" href="#">Another action</a>
                        <a class="dropdown-item" tabindex="-1" href="#">Something else here</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" tabindex="-1" href="#">Separated link</a>
                    </div>
                </li>
            </ul>
            <div class="ml-auto px-2 pt-2">
                <button v-if="authUserPermissions.includes('roles.store')" @click="openModalAddEdit('add')"
                    class="btn btn-info btn-sm">
                    <i class="fas fa-plus-square"> Enviar</i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
                Enviados
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_2">
                Recibidos
            </div>
            <!-- /.tab-pane -->
            <div class="tab-pane" id="tab_3">
                usuarios Bloqueados
            </div>
            <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
        </div>
    </div>
</template>

<script>

export default {
    mounte() {

    },

    data() {
        return {
            loaded: true
        }
    },
    methods: {

    },
    computed: {
        authUserPermissions() {
            return JSON.parse(sessionStorage.getItem('listPermissionsByAuthUser'));
        }
    }
}
</script>
