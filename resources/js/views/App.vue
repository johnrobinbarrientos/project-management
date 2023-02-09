<template>
    <div>
        <div v-if="ready">
            <template v-if="!$route.meta || !$route.meta.layout || $route.meta.layout == 'with-side-bar'">
                <SideBar></SideBar>
                <div class="bp-wrap">
                    <Header></Header>
                    <!-- content @s -->
                    <div class="nk-content">
                        <div class="container-fluid">
                            <div class="nk-content-inner">
                                <div class="nk-content-body">
                                     <router-view :key="$route.fullPath" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- content @e -->
                </div>
            </template>
            <template v-else>
                 <router-view/>
            </template>
        </div>
    </div>
</template>

<script>
import SideBar from './layouts/SideBar'
import Header from './layouts/Header'

export default {
    data: function () {
        return {
            authenticted: false,
            ready: false
        }
    },
    components: {
        SideBar,
        Header,
    },
    methods: {
        checkAuthenticatedUser: function () {
            var scope = this
            var route = window.location.pathname
            route = route.replace('/','')

            scope.GET('/api/check',{route: route}).then(response => {
                if (response.success) {
                    scope.$store.dispatch('authenticate',response.user)
                    scope.ready = true
                } else if (!response.success && response.code == 403) {
               
                    scope.$router.push({path: '/'});
                    scope.ready = true
                   
                    scope.$store.dispatch('changeHeader','Access Denied')
                    
                } else {
                    /*
                    localStorage.removeItem(window.TOKEN_KEY);
                    scope.$router.push({path: '/auth'})
                    scope.ready = true
                    */
                   setTimeout(function(){
                       scope.checkAuthenticatedUser();
                   },500);
                }

                
            });
        }
    },
    mounted() {
        var scope = this
        window.instance = scope;

        setTimeout(function(){
            scope.checkAuthenticatedUser();
        },1000);

        scope.$store.dispatch('fetchModuleUserSettings')
    
        if (!scope.$route.meta.protected) {
            scope.ready = true
            return;
        }   

        // scope.ready = true
    }
}
</script>

<style scoped>
.bp-wrap { margin-left:350px; padding:10px 20px; padding:0px; }
</style>