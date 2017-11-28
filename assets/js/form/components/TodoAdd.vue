<template>
    <div class="todo_add">
        <form class="todo_add">
            <q-field
                    label="Todo"
                    :error="$v.todo.$error"
                    error-label="This field is mandatory"
            >
                <q-input
                        type="text"
                        id="todo_add"
                        placeholder="Fill a todo"
                        v-model="form.todo"
                        @blur="$v.todo_add.$touch"
                />
            </q-field>

            <q-btn flat color="primary" @click="submit" icon="plus">ADD</q-btn>
        </form>
        <q-spinner-circles v-if="isLoading" size="15px"/>
    </div>
</template>

<script>
    import {
        QField,
        QInput,
        QBtn,
        Toast
    } from 'quasar-framework'
    import { required } from 'vuelidate/lib/validators'
    export default {
        name: 'TodoAdd',
        components: {
            QField,
            QInput,
            QBtn,
            Toast,
        },
        data() {
            return {
                isLoading: false,
                form: {
                    todo: '',
                },
            }
        },
        validations: {
            form: {
                todo: { required },
            }
        },
        methods: {
            submit () {
                this.$v.form.$touch()
                if (this.$v.form.$error) {
                    Toast.create.warning('Please review fields again.')
                    return
                }
                this.isLoading = true
                const body = new FormData(this.$v.form)
                const myHeaders = new Headers()
                const myInit = {
                    method: 'PUT',
                    headers: myHeaders,
                    mode: 'cors',
                    cache: 'default',
                    body: body,
                }
                fetch('/api/todo', myInit)
                    .then(response => {
                        this.isLoading = false
                        if (response.ok) {
                            Toast.create.positive('Todo created')
                            return res.json()
                        }
                        throw Error('Error adding todo' + response.error())
                    })
                    .then(json => {
                        this.todos.push(response)
                    })
                    .catch(e => {
                        Toast.create.negative(e.message)
                    })
            }
        }
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>