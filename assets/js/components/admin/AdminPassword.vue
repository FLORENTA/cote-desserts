<template>
    <transition name="fade">
        <div class="container w40 w95sm" id="password-form-container" v-show="isPasswordLoaded"></div>
    </transition>
</template>
<script>
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";
    import {spinner} from "../../mixins/spinner";

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
        },

        methods: {
            sendPasswordForm(e) {
                e.preventDefault();
                let $form = $(e.target)[0];
                let formData = new FormData($form);
                let $submitButton = $('#appbundle_password_submit');

                this.addButtonLoader($submitButton);

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: response => {
                        addAlert(response);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    },
                    complete: () => {
                        this.removeButtonLoader($submitButton);
                    }
                })
            }
        },

        mixins: [spinner],

        mounted() {
            $(document).on('submit', 'form[name="appbundle_password"]', this.sendPasswordForm);
        },

        beforeDestroy() {
            $(document).off('submit', 'form[name="appbundle_password"]', this.sendPasswordForm)
        }
    }
</script>