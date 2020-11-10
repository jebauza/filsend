<template>
    <footer class="main-footer">
        © {{ now.format('YYYY') }} Filsend
    </footer>
</template>

<script>
import moment from 'moment'

export default {
    props: ['basepath'],
    data() {
        return {
            now: moment()
        }
    }
}
</script>

<style>

</style>
