<template>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ titlePage }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li v-for="(breadcrumb, idx) in breadcrumbList" :key="idx" @click="routeTo(idx)"
                            :class="['breadcrumb-item', breadcrumb.link ? 'linked ' : 'active']">{{ breadcrumb.name }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    name: 'bread-crumb',
    mounted () { this.updateList() },
    watch: {
        '$route' () {
            this.updateList()
        }
    },
    data() {
        return {
           breadcrumbList: []
        }
    },
    methods: {
        routeTo (pRouteTo) {
            if (this.breadcrumbList[pRouteTo].link) this.$router.push(this.breadcrumbList[pRouteTo].link)
        },
        updateList () { this.breadcrumbList = this.$route.meta.breadcrumb }
    },
    computed: {
        titlePage() {
            return this.breadcrumbList.length ? this.breadcrumbList[this.breadcrumbList.length - 1].name : '';
        }
    },
}
</script>

<style scoped>
.linked {
    cursor: pointer;
    color: #007bff;
}
.linked:hover {
    color: #0056b3;
}
</style>
