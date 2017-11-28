<template>
    <div class="todo_remove">
        <q-btn flat color="primary" @click="remove" icon="delete"></q-btn>
        <q-spinner-circles v-if="isLoading" size="15px"/>
    </div>
</template>

<script>
    import {
        QBtn,
        Toast
    } from 'quasar-framework'
    import { required } from 'vuelidate/lib/validators'
    export default {
        name: 'TodoRemove',
        components: {
            QBtn,
            Toast,
        },
        props: ['id'],
        data() {
            return {
                isLoading: false,
            }
        },
        methods: {
            remove () {
                this.$v.form.$touch()
                if (!id && id !== 0) {
                    Toast.create.warning('Internal error [TodoRemove-missing-id].')
                    return
                }
                this.isLoading = true
                const myHeaders = new Headers()
                const myInit = { method: 'DELETE',
                    headers: myHeaders,
                    mode: 'cors',
                    cache: 'default'
                }
                fetch(`/api/todo/${this.id}`, myInit)
                    .then(response => {
                        this.isLoading = false
                        if (response.ok) {
                            this.$emit('remove', this.id)
                            return
                        }
                        Toast.create.negative('Error during remove process' + response.error())
                    })
            }
        }
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>