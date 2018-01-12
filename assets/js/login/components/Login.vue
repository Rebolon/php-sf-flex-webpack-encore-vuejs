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
                            type="text"
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
    import {
        QField,
        QIcon,
        QInput,
        QBtn,
        QSpinnerCircles,
        Toast,
    } from 'quasar-framework'
    import {required} from 'vuelidate/lib/validators'
    import getToken from '../../csrf_token'
    import isLoggedIn from '../../login'

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
        props: ['redirect',],
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
            isLoggedIn().then(isTrue => {
                Toast.create.info('You are logged in')

                if (this.redirect) {
                    this.$router.push(this.redirect)
                }
            })

            getToken(this).then(csrf_token => this.form.csrf = csrf_token)
        },
        validations: {
            form: {
                username: {required},
                password: {required},
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
                    'username': this.form.username,
                    'password': this.form.password,
                    'csrf': this.form.csrf,
                }
                const myHeaders = new Headers()
                myHeaders.append('Accept', 'application/json')
                myHeaders.append('Content-Type', 'application/json')
                const myInit = {
                    method: 'POST',
                    headers: myHeaders,
                    credentials: 'same-origin',
                    mode: 'cors',
                    cache: 'default',
                    body: JSON.stringify(body),
                }
                this.isLoading = true
                fetch('/demo/login/json', myInit).then(response => {
                    return response.json()
                }).then(response => {
                    this.isLoading = false
                    if (!response.error) {
                        localStorage.setItem('isLoggedIn', true)
                        if (this.redirect) {
                            this.$router.push(this.redirect)
                        }

                        return
                    }

                    localStorage.removeItem('isLoggedIn')

                    const msg = response.error.message ? response.error.message : response.error
                    if ('invalid token' === msg.toLowerCase()) {
                        getToken().then(res => Toast.create.warning(`${msg}, please try again`))
                    } else {
                        Toast.create.negative(`Invalid user name or password (${msg})`)
                    }
                })
            },
        },
    }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
