<template>
    <!-- wrap @s -->
    <div class="nk-wrap nk-wrap-nosidebar">
        <!-- content @s -->
        <div class="nk-content ">
            <div class="nk-split nk-split-page nk-split-md">
                <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-white w-lg-45">
                    <div class="absolute-top-right d-lg-none p-3 p-sm-5">
                        <a href="#" class="toggle btn btn-white btn-icon btn-light" data-target="athPromo"><em class="icon ni ni-info"></em></a>
                    </div>
                    <div class="nk-block nk-block-middle nk-auth-body">
                        <div class="brand-logo pb-5">
                            <a style="display:none;" href="html/index.html" class="logo-link">
                                <img class="logo-light logo-img logo-img-lg" src="/assets/images/demo/logo.png" srcset="/assets/images/demo/logo2x.png 2x" alt="logo">
                                <img class="logo-dark logo-img logo-img-lg" src="/assets/images/demo/logo-dark.png" srcset="/assets/images/demo/logo-dark2x.png 2x" alt="logo-dark">
                            </a>
                            <a @click="$router.push({path: '/'})" onclick="event.preventDefault()"  href="/" class="logo-link">
                                <strong style="font-size:30px;">SIGNUP KNOXVILLE</strong>
                            </a>
                        </div>

                        <template v-if="formdata.response_status == 'success'">
                            <div class="nk-block-head">
                                <div class="nk-block-head-content">
                                    <h5 class="nk-block-title">Email Sent!</h5>
                                    <div class="nk-block-des text-success">
                                        <p>We have sent you an email to retrieve your account, please check your <strong>{{ formdata.email }}</strong> inbox.</p> 
                                    </div>
                                </div>
                            </div><!-- .nk-block-head -->
                        </template>
                        <template v-if="formdata.response_status == '' || formdata.response_status == 'error'">
                            <div class="nk-block-head">
                                <div class="nk-block-head-content">
                                    <h5 class="nk-block-title">Reset password</h5>
                                    <div class="nk-block-des">
                                        <p>If you forgot your password, well, then we’ll email you instructions to reset your password.</p> 
                                    </div>
                                    <div v-if="formdata.response_status == 'success'" class="alert alert-success">
                                        <strong>Success!</strong>
                                        {{ formdata.response_message }}
                                    </div>
                                    <div v-if="formdata.response_status == 'error'" class="alert alert-danger">
                                        <strong>Error!</strong>
                                        {{ formdata.response_message }}
                                    </div>
                                </div>
                            </div><!-- .nk-block-head -->
                            <form v-on:submit.prevent="send()" action="/" class="form-validate is-alter" autocomplete="off" novalidate="novalidate">
                                <div class="form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="email-address">Email Address</label>
                                        <a class="link link-primary link-sm" tabindex="-1" href="#">Need Help?</a>
                                    </div>
                                    <div class="form-control-wrap">
                                        <input v-model="formdata.email"  type="text" class="form-control form-control-lg" required="" id="email-address" placeholder="Enter your email address or username">
                                    </div>
                                </div><!-- .form-group -->
        
                                <div class="form-group">
                                    <button class="btn btn-lg btn-primary btn-block">Submit</button>
                                </div>
                            </form><!-- form -->
                            <div class="form-note-s2 pt-4"><a @click="$router.push({path: '/auth'})" href="/auth" onclick="event.preventDefault()">Return to Login</a></div>
                        </template>

                    </div><!-- .nk-block -->
                    <div class="nk-block nk-auth-footer">
                        <div class="nk-block-between" style="justify-content:center;">
                            <ul class="nav nav-sm">
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Terms & Condition</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#">Privacy Policy</a>
                                </li>
                            </ul><!-- nav -->
                        </div>
                        <div class="mt-3 text-center">
                            <p>&copy; 2021 SIGNUP KNOXVILLE. All Rights Reserved.</p>
                        </div>
                    </div><!-- nk-block -->
                </div><!-- nk-split-content -->
                <div class="nk-split-content nk-split-stretch bg-lighter d-flex toggle-break-lg toggle-slide toggle-slide-right" data-content="athPromo" data-toggle-screen="lg" data-toggle-overlay="true">
                    
                </div><!-- nk-split-content -->
            </div><!-- nk-split -->
        </div>
        <!-- wrap @e -->
    </div>
    <!-- content @e -->
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
        send: function () {
            var scope = this

            scope.formdata.response_status = ''
            scope.axios
            .post('/api/forgot-password', scope.formdata)
            .then(response => {
                var data = response.data

                if (data.success) {
                    scope.formdata.response_status = 'success'
                    scope.formdata.response_message = data.message
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
