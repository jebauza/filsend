<template>
    <div class="content-wrapper" style="min-height: 1184px;">

        <!-- BreadCrumb -->
        <bread-crumb></bread-crumb>

        <!-- Main content -->
        <section class="content">
            <transition name="slide-fade" mode="out-in">

                <router-view></router-view>

            </transition>
        </section>

    </div>
</template>

<script>
import BreadCrumb from './BreadCrumb';

export default {
    props: ['basepath'],
    components: {BreadCrumb}
}
</script>

<style>

</style>
