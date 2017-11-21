<template>
  <div class="login">
    <form class="login">
      <label class="title">
        <h3 class="welcome">Welcome to</h3>
        Company Product Name
        <h5 class="hint">Use your Company ID to sign in or create one now</h5>
      </label>
      <div class="login-group">
        <q-field
                icon="account_circle"
                label="Username"
                :error="$v.username.$error"
                error-label="This field is mandatory"
                inset="icon"
        >
          <q-input
                  type="text"
                  class="username"
                  id="login_username"
                  placeholder="Username"
                  v-model="form.username"
                  @blur="$v.username.$touch"
          />
        </q-field>

        <q-field
                icon="key"
                label="Password"
                :error="$v.password.$error"
                error-label="This field is mandatory"
                inset="icon"
        >
          <q-input
                  type="text"
                  class="password"
                  id="login_password"
                  placeholder="Password"
                  v-model="form.password"
                  @blur="$v.username.$touch"
                  @keyup.enter="submit"
          />
        </q-field>

        <div class="error active">
          You can login with test_user/test_pwd
        </div>

        <q-btn flat color="primary" @click="submit">NEXT</q-btn>
      </div>
    </form>

    <q-spinner-circles v-if="isLoading" size="150px"/>
  </div>
</template>

<script>
import {
  QField,
  QInput,
  QBtn,
  QSpinnerCircles,
  Toast
} from 'quasar-framework'
import { required } from 'vuelidate/lib/validators'

export default {
  name: 'Login',
  components: {
    QField,
    QInput,
    QBtn,
    QSpinnerCircles,
    Toast,
  },
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
  validations: {
    form: {
      username: { required },
      password: { required },
    }
  },
  methods: {
    submit () {
      this.$v.form.$touch()

      if (this.$v.form.$error) {
        Toast.create.warning('Please review fields again.')

        return
      }

      const body = new FormData(this.$v.form)
      const myHeaders = new Headers()
      const myInit = {
          method: 'POST',
          headers: myHeaders,
          mode: 'cors',
          cache: 'default',
          body: body,
      }

      fetch('/api/login', myInit)
      .then(response => {
        if (response.ok) {
            // @todo check the uri : does it contain login or not ?
          router.push('/demo/form')

          return
        }

        Toast.create.negative('Invalid user name or password')
      })
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
