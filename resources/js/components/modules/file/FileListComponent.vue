<template>
    <div class="row">
        <div class="col-12">
            <div class="card card-primary card-outline collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">Expandable</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-success"><i class="fas fa-download"></i></button>
                        <button type="button" class="btn btn-tool text-primary" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    The body of the card
                </div>
            </div>
        </div>
    </div>
</template>

<script>


export default {

    props: ['type'],
    mounted() {
        this.getFiles();
    },
    data() {
        return {

            fullscreenLoading: false
        }
    },
    methods: {
        getFiles() {

        }
    },

}
</script>
