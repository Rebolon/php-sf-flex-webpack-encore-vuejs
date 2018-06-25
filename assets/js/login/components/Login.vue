<template>
    <div class="login">
        <form class="login" @submit="prevent">
            <label class="title">
                <h5 class="welcome">Welcome to</h5>
                the demo of Symfony 3.3+ / Webpack encore / Vuejs / Quasar
                <h6 class="hint">You can login with test_js/test</h6>
            </label>
            <div class="login-group">
                <q-field
                        icon="account_circle"
                        label="Username"
                        :error="$v.form.username.$error"
                        error-label="This field is mandatory"
                        inset="icon"
                >
                    <q-input
                            type="text"
                            placeholder="Username"
                            name="username"
                            v-model="form.username"
                            @blur="$v.form.username.$touch"
                    />
                </q-field>

                <q-field
                        icon="https"
                        label="Password"
                        :error="$v.form.password.$error"
                        error-label="This field is mandatory"
                        inset="icon"
                >
                    <q-input
                            type="password"
                            placeholder="Password"
                            name="password"
                            v-model="form.password"
                            @blur="$v.form.password.$touch"
                            @keyup.enter="submit"
                    />
                </q-field>

                <q-btn flat color="primary" @click="submit" :disabled="isLoading">LOGIN</q-btn>
            </div>
        </form>

        <q-spinner-circles v-if="isLoading" size="20px"/>
    </div>
</template>

<script>
import { QField, QIcon, QInput, QBtn, QSpinnerCircles, Notify } from 'quasar-framework/dist/quasar.mat.esm'
import { required } from 'vuelidate/lib/validators'
import getToken from '../../lib/csrfToken'
import isLoggedIn from '../../lib/login'
import { loginInfos } from '../../lib/config'
import axios from '../../lib/axiosMiddlewares'

export default {
    name: 'Login',
    components: {
        QField,
        QIcon,
        QInput,
        QBtn,
        QSpinnerCircles,
        Notify,
    },
    props: ['redirect', 'loginUri', 'loggedInUri'],
    data() {
        return {
            msg: 'Login',
            isLoading: true,
            form: {
                username: '',
                password: '',
            },
        }
    },
    created() {
        this.isLoading = true
        if (!this.loginUri) {
            this.loginUri = loginInfos.json
        }

        isLoggedIn(undefined, this.loggedInUri)
            .then(() => {
                Notify.create({
                    message: 'You are logged in.',
                    type: 'info'
                })

                if (this.redirect) {
                    if (this.redirect.startsWith('http')) {
                        location.href = this.redirect

                        return
                    }

                    this.$router.push(this.redirect)
                }
            })
            .catch(err => {
                Notify.create({
                    message: 'You need to log in to access the app.',
                    type: 'info'
                })
            })

        getToken(this)
            .catch(err => {
                console.warn('No token retrieved', err)
            })
            .finally(() => this.isLoading = false)
    },
    validations: {
        form: {
            username: { required },
            password: { required },
        },
    },
    methods: {
        prevent(ev) {
            ev.preventDefault()
            console.log('login submitted')
        },
        submit(ev) {
            ev.stopPropagation()
            this.$v.form.$touch()
            if (this.$v.form.$error) {
                Notify.create({
                    message: 'Please review fields again.',
                    type: 'warning'
                })

                return
            }
            const body = {}
            body[loginInfos.loginUsernamePath] = this.form.username
            body[loginInfos.loginPasswordPath] = this.form.password

            const config = {
                method: 'POST',
                data: body,
            }
            this.isLoading = true
            axios.request(this.loginUri, config)
                .then(response => {
                    console.info('Login.Vue', response)

                    // pattern used for login json (not jwt)
                    localStorage.setItem('isLoggedIn', true)

                    // save JWT Token
                    if (response.headers['content-type'] === 'application/json'
                        && response.data
                        && response.data.token) {
                        localStorage.setItem('rememberMe', JSON.stringify(response.data))
                    }

                    if (this.redirect) {
                        if (this.redirect.startsWith('http')) {
                            location.href = this.redirect

                            return
                        }

                        this.$router.push(this.redirect)
                    }
                })
                .catch(err => {
                    // @todo find a way fot the action controller to return the same kind of exception than standard one
                    // to prevent those kind of code
                    // i can receive an HTTP 401 from the framework, or from the loginController :
                    let errMsg = err.response.statusText
                    let code = err.response.status
                    if (err.response.data) {
                        if (err.response.data.error.exception) {
                            err.response.data.error.exception["0"].message.startsWith('Exception: UsernameNotFoundException')
                            errMsg = 'User not found'
                        } else {
                            if (err.response.data.message) {
                                errMsg = err.response.data.message
                            }

                            if (err.response.data.code) {
                                code = err.response.data.code
                            }
                        }
                    }

                    /**
                     * 401 Unauthorized: Invalid credentials
                     * 420 : Token mandatory
                     *
                     */
                    switch(code) {
                        case 403:
                            Notify.create({
                                message: 'Wrong credentials, please try again',
                                type: 'warning'
                            })
                            break;
                        case 404:
                            Notify.create({
                                message: `Wrong credentials, please try again (${errMsg})`,
                                type: 'warning'
                            })
                            break;
                        case 420:
                            Notify.create({
                                message: `Invalid user name or password (${errMsg})`,
                                type: 'negative'
                            })
                            break;
                        case 423:
                            getToken()
                                .then(res => {
                                    Notify.create({
                                        message: `Invalid token, please try again`,
                                        type: 'warning'
                                    })
                                })
                                .catch(err => console.warn('Login methos submit', 'getToken', err))
                            break;
                        default:
                            console.warn(err.response)
                            Notify.create({
                                message: `Unknown error ${err.response.status} ${errMsg}`,
                                type: 'warning'
                            })
                    }
                })
                .finally(() => {
                    this.isLoading = false
                })
        },
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
