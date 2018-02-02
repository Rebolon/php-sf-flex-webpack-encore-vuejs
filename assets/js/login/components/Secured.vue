<template>
    <div class="books">
        <label class="title">
            <h5 class="title">Secured page</h5>
        </label>
        <q-toolbar color="primary">
            <q-toolbar-title>
                Hello {{ user.username }}
            </q-toolbar-title>
        </q-toolbar>
    </div>
</template>

<script>
import { QToolbar, QToolbarTitle, Toast } from 'quasar-framework'
import { IsLoggedInObservable } from '../../lib/login'
export default {
    name: 'Secured',
    components: {
        QToolbar,
        QToolbarTitle,
        Toast
    },
    data() {
        return {
            user: {},
        }
    },
    created() {
        const defaultUser = {
            user: {
                username: 'inconnu'
            }
        }

        this.user = defaultUser

        if (typeof localStorage === undefined) {
            Toast.create.warning(`No localStorage feature available in the browser`)
        }

        IsLoggedInObservable.subscribe(isLoggedIn => {
            if (!isLoggedIn) {
                this.user = defaultUser
            } else {
                this.user = isLoggedIn.me
            }
        })
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
