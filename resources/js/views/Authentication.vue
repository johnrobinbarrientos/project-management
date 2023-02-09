<template>
    <!-- wrap @s -->
    <div style=" max-width:500px; border:1px solid #ccc; padding:40px; margin:0px auto; margin-top:80px;">
        <div>
            <div  v-if="formdata.response_status == 'success'" class="alert alert-success">{{ formdata.response_message }}</div>
            <div  v-if="formdata.response_status == 'error'" class="alert alert-error">{{ formdata.response_message }}</div>
            <form v-on:submit.prevent="authenticate()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="email-address">Email Address</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.email"  type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter your email address or username">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="password">Password</label>
                    </div>
                    <div class="form-control-wrap">
                        <input v-model="formdata.password" a type="password" class="form-control form-control-lg" required="" id="password" placeholder="Enter your password">
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-blue">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            formdata: {
                response_status: '', // error or success
                response_message: '', 
                email: '',
                password: '',
            },
            accept_terms: false
        }
    },
    methods: {
        authenticate: function () {
            var scope = this
            scope.formdata.response_status = ''
            scope.axios
            .post('/api/login', scope.formdata)
            .then(response => {
                var data = response.data
  
                if (data.success) {
                    scope.formdata.response_status = 'success'
                    scope.formdata.response_message = data.message
                    localStorage.setItem(window.TOKEN_KEY, data.token);
    
                    scope.$store.dispatch('authenticate',data.user)
                    scope.$store.dispatch('fetchModuleUserSettings')
                    
                    setTimeout(function(){
                        if (scope.$route.query.redirect && scope.$route.query.redirect != '') {
                            location.href = scope.$route.query.redirect
                        } else {
                            if (data.user.type == 'admin') {
                                scope.$router.push({path: '/'})
                            } else {
                                scope.$router.push({path: '/'})
                            }
                        }
                    },1500);
                    
                    return
                }

                scope.formdata.response_message = data.message
            })
            .catch(function (error) {
                var response = error.response
                var data = error.response.data
                var code = error.response.status

                scope.formdata.response_status = 'error'
                scope.formdata.response_message = (data.message) ? data.message : response.statusText
            })
        },
    },
    mounted() {
    }
}
</script>

<style scoped>
.alert { margin-top:10px; }
</style>
