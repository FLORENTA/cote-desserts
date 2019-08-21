<template>
    <transition name="fade">
        <div class="container w40 w95sm" id="password-form-container" v-show="isPasswordLoaded"></div>
    </transition>
</template>
<script>
    import {Routing} from './../../js/routing';

    export default {
        name: 'admin-password',

        data() {
            return {
                isPasswordLoaded: false
            }
        },

        created() {
            $.get(Routing.generate('fetch_password_form'), response => {
                $('#password-form-container').append(response);
                this.isPasswordLoaded = true;
            });
        }
    }
</script>