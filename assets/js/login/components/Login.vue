<template>
    <div class="login">
        <form class="login" @submit="prevent">
            <label class="title">
                <h5 class="welcome">Welcome to</h5>
                the demo of Symfony 3.3+ / Webpack encore / Vuejs / Quasar
                <h6 class="hint">You can login with test_user/test_pwd</h6>
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

                <q-btn flat color="primary" @click="submit" :disabled="!form.csrf">LOGIN</q-btn>
            </div>
        </form>

        <q-spinner-circles v-if="isLoading" size="20px"/>
    </div>
</template>

<script>
import { QField, QIcon, QInput, QBtn, QSpinnerCircles, Toast } from 'quasar-framework'
import { required } from 'vuelidate/lib/validators'
import getToken from '../../csrf_token'
import isLoggedIn from '../../login'
import axios from '../../axios_middlewares'

export default {
    name: 'Login',
    components: {
        QField,
        QIcon,
        QInput,
        QBtn,
        QSpinnerCircles,
        Toast,
    },
    props: ['redirect'],
    data() {
        return {
            msg: 'Login',
            isLoading: false,
            form: {
                username: '',
                password: '',
                csrf: '',
            },
        }
    },
    created() {
        isLoggedIn()
            .then(isTrue => {
                Toast.create.info('You are logged in')

                if (this.redirect) {
                    this.$router.push(this.redirect)
                }
            })

        getToken(this)
            .then(csrf_token => (this.form.csrf = csrf_token))
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
                Toast.create.warning('Please review fields again.')

                return
            }
            const body = {
                username: this.form.username,
                password: this.form.password,
                csrf: this.form.csrf,
            }
            const config = {
                method: 'POST',
                data: body,
            }
            this.isLoading = true
            axios.request('/demo/login/json', config)
                .then(response => {
                    console.info('Login.Vue', response)
                })
                .catch(err => {
                    // @todo find a way fot the action controller to return the same kind of exception than standard one
                    // to prevent those kind of code
                    // i can receive an HTTP 401 from the framework, or from the loginController :
                    let errMsg = err.statusText
                    if (err.response.data && err.response.data.error) {
                        errMsg = err.response.data.error
                    }

                /**
                 * 401 Unauthorized: Invalid credentials
                 * 420 : Token mandatory
                 *
                 */
                switch(err.response.status) {
                        case 401:
                            let message = ['Token mandatory', 'Invalid token', ].find(msg => msg.toLowerCase() === errMsg.toLowerCase()) ?
                                errMsg : 'Wrong credentials, please try again'
                            Toast.create.warning(message)
                            break;
                        case 420:
                            if (['Token mandatory', 'Invalid token', ].find(msg => msg.toLowerCase() === errMsg.toLowerCase())) {
                                getToken().then(res => Toast.create.warning(`${errMsg}, please try again`))
                            } else {
                                Toast.create.negative(`Invalid user name or password (${errMsg})`)
                            }
                            break;
                        default:
                            console.warn(err.response)
                            Toast.create.warning(`Unknown error ${err.response.status} ${errMsg}`)
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
