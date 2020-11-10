<template>
    <div class="row">
        <div v-for="(send, index) in sendings.data" class="col-12" :key="send.id">
            <div :class="['card', (typeList == 'send' ? 'card-primary' : 'card-warning'), 'card-outline', 'collapsed-card']">
                <div class="card-header">
                    <h3 class="card-title"><span :class="['right', 'badge', 'mr-2', (typeList == 'send' ? 'badge-primary' : 'badge-warning')]">{{ index+1 }}</span>
                        <small class="mr-3"> {{ moment(send.created_at, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm') }}</small>
                        <strong :class="typeList == 'send' ? 'text-primary' : 'text-warning'">{{ user(send) ? user(send).email : '' }}</strong>
                    </h3>
                    <div class="card-tools">
                        <el-tooltip class="item" effect="dark" content="Descargar" placement="bottom">
                            <button @click="downloadFile(send.file)" type="button" class="btn btn-tool text-success"><i class="fas fa-download"></i></button>
                        </el-tooltip>
                        <button type="button" :class="['btn', 'btn-tool', (typeList == 'send' ? 'text-primary' : 'text-warning')]" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <div class="row invoice-info">
                        <div class="col-sm-6 col-md-4 invoice-col">
                            <b><i class="fas fa-user"></i></b> {{ user(send) ? user(send).fullName : '' }}<br>
                        </div>
                        <div class="col-sm-6 col-md-4 invoice-col">
                            <b><i class="fas fa-envelope"></i></b> {{ user(send) ? user(send).email : '' }}<br>
                        </div>
                        <div class="col-12 col-md-4 invoice-col">
                            <b><i class="fas fa-file"></i></b> {{ send.file.name }}<br>
                        </div>
                        <div class="col-12 invoice-col mt-2">
                            <b>Mensaje:</b><br> {{ send.message }}<br>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <pagination :class="'col-12 mt-2'" :align="'center'" :limit="5" :data="sendings" @pagination-change-page="getFiles"></pagination>
    </div>
</template>

<script>


export default {

    props: ['typeList'],
    created() {
        this.getFiles();
    },
    data() {
        return {
            sendings: {data: []},
            fullscreenLoading: false
        }
    },
    methods: {
        getFiles(page = 1) {
            let url = '/cmsapi/files/'
            if(this.typeList == 'send') {
                url += 'sendings';
            }else {
                url += 'received';
            }

            axios.get(url, {
                params: {
                    page: page
                }
            })
            .then(res => {
                this.sendings = res.data;
            })
        },
        downloadFile(file) {
            axios({
                url: `/cmsapi/files/${file.id}/download`,
                method: 'GET',
                responseType: 'blob',
            }).then((response) => {
                    let fileURL = window.URL.createObjectURL(new Blob([response.data]));
                    let fileLink = document.createElement('a');

                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', file.name);
                    document.body.appendChild(fileLink);

                    fileLink.click();
            });
        },
        user(send) {
            if(this.typeList == 'send') {
                return send.to_user;
            }else {
                return send.from_user;;
            }
        }
    },

}
</script>
