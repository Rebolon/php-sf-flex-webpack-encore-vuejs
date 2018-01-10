<template>
    <div class="books">
        <label class="title">
            <h5 class="title">Secured page</h5>
        </label>
        <q-toolbar color="primary">
            <q-toolbar-title>
                Hello user (need a way to retreive info from api : isLoggedIn route to be improved and send personal info)
            </q-toolbar-title>
        </q-toolbar>
    </div>
</template>

<script>
    import {
        QToolbar,
        QToolbarTitle,
    } from 'quasar-framework'
    export default {
        name: 'Secured',
        components: {
            QToolbar,
            QToolbarTitle,
        },
        data() {
            return {
                user: {},
                isLoading: true,
            };
        },
    };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
