<template>
    <transition name="fade">
        <div class="container w40 w95sm" v-show="isLoginFormLoaded">
            <div id="login-container"></div>
            <server-message v-bind:displayMessage="displayMessage">{{ message }}</server-message>
        </div>
    </transition>
</template>

<script>
    import Mixins from '../../mixins';
    import { mapState } from 'vuex';
    import ServerMessage from "../ServerMessage";
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";

    export default {
        name: 'admin-login',

        data() {
            return {
                isLoginFormLoaded: false
            }
        },

        components: {ServerMessage},

        computed: {
            ...mapState({
                displayMessage: state => state.displayMessage,
                message: state => state.message,
            }),
        },

        mixins: [Mixins],

        mounted() {
            $.ajax({
                type: 'GET',
                url: Routing.generate('fetch_login_form'),
                success: response => {
                    $('#login-container').append(response);
                    this.isLoginFormLoaded = true;
                }
            });

            $(document).on('submit', 'form[name="appbundle_login"]', e => {
                e.preventDefault();
                let $form = $(e.target)[0];
                let formData = new FormData($form);

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    processData: false,
                    contentType: false,
                    data: formData,
                    beforeSend: () => {
                        this.$store.commit('displaySendingRequest');
                    },
                    success: data => {
                        if (!'token' in data) {
                            addAlert(data);
                        } else {
                            localStorage.setItem('token', data.token);
                            addAlert(Translator.trans('login.authentication.success'));
                            this.$router.push({name: 'home_admin'});
                        }
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    }
                });
            });
        }
    }
</script>