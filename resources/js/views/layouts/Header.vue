<template>
    <div>
        <div class="bp-heading">
            <div><h2>{{ header }}</h2></div>
            <div><a class="btn-logout" href="javascript:void(0);" @click="logout()">Logout</a></div>
        </div>
    </div>
    <!-- main header @e -->
</template>

<script>
import moment from 'moment'

export default {
    data: function () {
        return {
            notifications: '',
        }
    },
    computed: {
        header: function () {
            return this.$store.getters.getHeader
        },
        logout: function () {
            var scope = this
            scope.GET('/api/logout').then(function (res) {
                scope.$router.push({ path: '/auth'})
                localStorage.removeItem(window.TOKEN_KEY)
            });
        }
    },
    filters: {

    },
    methods: {
 
    },
    mounted() {
        var scope = this
    }
}
</script>

<style scoped>
.bp-heading { display:flex; justify-content:space-between; background:#fff; height:40px; line-height: 40px; padding:0px 20px; border-bottom: 1px solid #e9ebf0; }
.bp-heading h2 { font-size:20px; color:#333; }
.bp-heading .btn-logout { color:#000; text-decoration: none;}
</style>