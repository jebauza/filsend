<template>
    <div class="row">
        <div v-for="(send, index) in sendings.data" class="col-12" :key="send.id">
            <div class="card card-primary card-outline collapsed-card">
                <div class="card-header">
                    <h3 class="card-title"><span class="right badge badge-primary mr-2">{{ index+1 }}</span>
                        <small class="mr-3"> {{ moment(send.created_at, 'YYYY-MM-DD HH:mm:ss').format('DD/MM/YYYY HH:mm') }}</small>
                        <strong class="text-primary">{{ send.to_user ? send.to_user.email : '' }}</strong>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-success"><i class="fas fa-download"></i></button>
                        <button type="button" class="btn btn-tool text-primary" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <div class="row invoice-info">
                        <div class="col-sm-6 col-md-4 invoice-col">
                            <b><i class="fas fa-user"></i></b> {{ send.to_user ? send.to_user.fullName : '' }}<br>
                        </div>
                        <div class="col-sm-6 col-md-4 invoice-col">
                            <b><i class="fas fa-envelope"></i></b> {{ send.to_user ? send.to_user.email : '' }}<br>
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
                url += 'sendings';
            }

            axios.get(url, {
                params: {
                    page: page
                }
            })
            .then(res => {
                this.sendings = res.data;
            })
        }
    },

}
</script>
