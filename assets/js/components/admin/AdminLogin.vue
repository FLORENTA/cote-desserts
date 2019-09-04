<template>
    <transition name="fade">
        <div class="container w40 w95sm" v-show="isLoginFormLoaded">
            <div id="login-container"></div>
            <server-message v-bind:displayMessage="displayMessage">{{ message }}</server-message>
        </div>
    </transition>
</template>

<script>
    import { mapState } from 'vuex';
    import ServerMessage from "../ServerMessage";
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";
    import {Spinner} from "../../mixins/spinner";

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

        mixins: [Spinner],

        methods: {
            handleLoginFormSubmission(e) {
                e.preventDefault();
                let $form = $(e.target)[0];
                let formData = new FormData($form);
                let $submitButton = $('#appbundle_login_submit');

                this.addButtonLoader($submitButton);

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
                    },
                    complete: () => {
                        this.removeButtonLoader($submitButton);
                    }
                });
            }
        },

        mounted() {
            $.get(Routing.generate('fetch_login_form'), response => {
                $('#login-container').append(response);
                this.isLoginFormLoaded = true;
            });

            $(document).on('submit', 'form[name="appbundle_login"]', this.handleLoginFormSubmission);
        },

        beforeDestroy() {
            $(document).off('submit', 'form[name="appbundle_login"]', this.handleLoginFormSubmission);
        }
    }
</script>