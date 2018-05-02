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
        <q-spinner-circles v-if="isLoading" size="20px"/>
    </div>
</template>

<script>
import { QToolbar, QToolbarTitle, Notify, QSpinnerCircles, } from 'quasar-framework/dist/quasar.mat.esm'
import isLoggedIn, { IsLoggedInObservable } from '../../lib/login'
export default {
    name: 'Secured',
    components: {
        QToolbar,
        QToolbarTitle,
        QSpinnerCircles,
        Notify
    },
    data() {
        return {
            user: {},
            isLoading: true,
        }
    },
    created() {
        const defaultUser = {
            user: {
                username: 'inconnu'
            }
        }

        this.user = defaultUser

        if (typeof localStorage == 'undefined') {
            Notify.create({
                message: `No localStorage feature available in the browser`,
                type: 'warning'
            })
        }

        IsLoggedInObservable.subscribe(isLoggedIn => {
            if (!isLoggedIn
                || !isLoggedIn.me) {
                this.user = defaultUser
            } else {
                this.user = isLoggedIn.me
            }
            this.isLoading = false
        }, err => {
            // @todo we may have different message belongs to err
            Notify.create({
                message: 'You need to log in to access the app.',
                type: 'info'
            })
            this.$router.push('Login')
        })
        isLoggedIn()
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
