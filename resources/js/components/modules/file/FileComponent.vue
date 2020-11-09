<template>
    <div class="card">
        <!-- Carga de datos -->
        <div v-if="!loaded" class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>

        <div class="card-header d-flex p-0">
            <ul class="nav nav-pills p-2">
                <li class="nav-item"><a class="nav-link active" href="#tab_send" data-toggle="tab">Enviados</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_received" data-toggle="tab">Recibidos</a></li>
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
                <button v-if="authUserPermissions.includes('roles.store')" @click="showModalSendFile()"
                    class="btn btn-info btn-sm">
                    <i class="fas fa-plus-square"> Enviar</i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="tab-content">

                <div class="tab-pane active" id="tab_send">
                    <file-list ref="fileListSend" :type="'send'"></file-list>
                </div>

                <div class="tab-pane" id="tab_received">
                    Recibidos
                </div>

                <div class="tab-pane" id="tab_3">
                    usuarios Bloqueados
                </div>
            </div>
        </div>

        <!-- MODAL -->
        <div class="modal fade" id="modalSendFile" tabindex="-1" role="dialog" aria-hidden="true" >
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Enviar Archivo</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form class="needs-validation" v-on:submit.prevent="sendFile()">
                        <div class="modal-body">

                            <div class="form-row">
                                <el-autocomplete
                                    class="inline-input col-12"
                                    v-model="search"
                                    :fetch-suggestions="querySearch"
                                    placeholder="Usuario"
                                    :trigger-on-focus="false"
                                    @select="handleSelect">
                                    <i
                                        class="el-icon-search el-input__icon"
                                        slot="suffix">
                                    </i>
                                </el-autocomplete>
                                <div class="form-group col-12 pt-1">
                                    <label for="message" :class="['control-label', errors.message ? 'text-danger' : '']">Mensaje</label>
                                    <textarea v-model="formSendFile.message" :class="['form-control', errors.message ? 'is-invalid' : '']" name="message"></textarea>
                                    <small v-if="errors.message" class="form-control-feedback text-danger">
                                        {{ errors.message[0] }}
                                    </small>
                                </div>
                                <div class="col-12">
                                    <div class="custom-file">
                                        <input type="file" @change="getFile" :class="['custom-file-input', errors.file ? 'is-invalid' : '']" id="customFileLangHTML">
                                        <label :class="['custom-file-label', errors.file ? 'text-danger' : '']" for="customFileLangHTML" data-browse="Elegir Archivo">
                                            {{ (formSendFile.file ? formSendFile.file.name : 'Selecione Archivo') }}
                                        </label>
                                        <small v-if="errors.file" class="form-control-feedback text-danger">
                                            {{ errors.file[0] }}
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" v-loading.fullscreen.lock="fullscreenLoading">Enviar</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>


    </div>
</template>

<script>
import FileList from './FileListComponent';

export default {
    components: {FileList},
    created() {
        this.getUsersCanSend();
    },
    data() {
        return {
            search: '',
            users_can_send: [],
            formSendFile: {
                to_user_id: '',
                message: '',
                file: '',
            },
            errors: {},
            loaded: true,
            fullscreenLoading: false
        }
    },
    methods: {
        getUsersCanSend() {
            const url = '/cmsapi/files/sendings/can_send_users';
            axios.get(url)
            .then(res => {
                this.users_can_send = res.data
            })
            .catch(err => {
                console.error(err);
            })
        },
        showModalSendFile() {
            this.getUsersCanSend();
            $('#modalSendFile').modal('show');
        },
        getFile(e) {
            this.formSendFile.file = e.target.files[0];
        },
        querySearch(queryString, cb) {
            let users = this.users;
            let results = users;
            if(queryString) {
                results = users.filter((u) => {
                    return (u.value.toLowerCase().indexOf(queryString.toLowerCase()) !== -1);
                });
            }

            // call callback function to return suggestions
            cb(results);
        },
        handleSelect(item) {
            this.formSendFile.to_user_id = item.id
        },
        sendFile() {
            this.fullscreenLoading = true;
            const config = { headers: { 'content-type': 'multipart/form-data' } };
            let formData = new FormData;
            for (const property in this.formSendFile) {
                formData.append(property, this.formSendFile[property]);
            }

            const url = '/cmsapi/files/sendings/store'
            axios.post(url, formData, config)
            .then(res => {
                this.fullscreenLoading = false;
                Swal.fire({
                    title: res.data.msg,
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#modalSendFile').modal('hide');
                this.formSendFile = {
                    to_user_id: '',
                    message: '',
                    file: '',
                };
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
            })
        }

    },
    computed: {
        authUserPermissions() {
            return JSON.parse(sessionStorage.getItem('listPermissionsByAuthUser'));
        },
        users() {
            if(this.users_can_send.length) {
                return this.users_can_send.map(user => {
                    return {
                        value: `${user.email}, ${user.fullName}`,
                        id: user.id
                    };
                });
            }

            return [];
        },
        toUser() {
            if(this.formSendFile.to_user_id) {
                return this.users_can_send.find(u => u.id === this.formSendFile.to_user_id);
            }
            return null;
        }
    }
}
</script>
