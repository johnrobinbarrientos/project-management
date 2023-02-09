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
                                <strong style="font-size:30px;">SKELETON</strong>
                            </a>
                        </div>
                        <div class="nk-block-head">
                            <div class="nk-block-head-content">
                                <h5 class="nk-block-title">Register</h5>
                                <div class="nk-block-des">
                                    <p>Create <strong>Skeleton</strong> Account</p>
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
                        <form v-if="formdata.response_status != 'success'" v-on:submit.prevent="register()" action="/">
                            <div class="form-group">
                                <label class="form-label" for="name">First Name</label>
                                <div class="form-control-wrap">
                                    <input v-model="formdata.first_name" type="text" class="form-control form-control-lg" id="name" placeholder="Enter your first name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="name">Last Name</label>
                                <div class="form-control-wrap">
                                    <input v-model="formdata.last_name" type="text" class="form-control form-control-lg" id="name" placeholder="Enter your last name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="email">Email Address</label>
                                <div class="form-control-wrap">
                                    <input v-model="formdata.email" type="text" class="form-control form-control-lg" id="email" placeholder="Enter your email address">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="phone">Phone Number</label>
                                <div class="form-control-wrap">
                                    <input v-model="formdata.phone" type="text" class="form-control form-control-lg" id="phone" placeholder="Enter your phone number">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="password">Password</label>
                                <div class="form-control-wrap">
                                    <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                    </a>
                                    <input v-model="formdata.password" type="password" class="form-control form-control-lg" id="password" placeholder="Enter your password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="password">Re-Type Password</label>
                                <div class="form-control-wrap">
                                    <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password2">
                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                    </a>
                                    <input v-model="formdata.password2" type="password" class="form-control form-control-lg" id="password2" placeholder="Enter your password">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-control-xs custom-checkbox">
                                    <input v-model="accept_terms" type="checkbox" class="custom-control-input" id="checkbox">
                                    <label class="custom-control-label" for="checkbox">I agree to RealEstate <a tabindex="-1" href="#">Privacy Policy</a> &amp; <a tabindex="-1" href="#"> Terms.</a></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-block">Register</button>
                            </div>
                        </form><!-- form -->
                        <div class="form-note-s2 pt-4"> Already have an account ? <a @click="$router.push({path: '/auth'})" onclick="event.preventDefault()"  href="/auth" ><strong>Sign in instead</strong></a>
                        </div>
                     
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
                first_name: '',
                last_name: '',
                password: '',
                password2: '',
            },
            accept_terms: false
        }
    },
    methods: {
        register: function () {
            var scope = this

            if (!scope.accept_terms) {
                scope.formdata.response_status = 'error'
                scope.formdata.response_message = 'You must accept our terms & conditions'
                return
            }

            scope.formdata.response_status = ''
            scope.axios
            .post('/api/signup', scope.formdata)
            .then(response => {
                var data = response.data
                console.log(data)
                if (data.success) {
    
                    scope.formdata.response_status = 'success'
                    scope.formdata.response_message = data.message
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
        console.log('Component mounted.')
    }
}
</script>

<style scoped>
.alert { margin-top:10px; }
</style>
