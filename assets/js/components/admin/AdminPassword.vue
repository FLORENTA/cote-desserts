<template>
    <transition name="fade">
        <div class="container w40 w95sm" id="password-form-container" v-show="isPasswordLoaded"></div>
    </transition>
</template>
<script>
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";

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
            addButtonLoader($button) {
                if (!$button.hasClass('fa-spinner')) {
                    $button.append(
                        $("<span>&nbsp;<i class='fa fa-spinner fa-spin'></i><span>")
                    );
                }
            },

            removeButtonLoader($button) {
                $button.find('span').remove();
            },

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

        mounted() {
            $(document).on('submit', 'form[name="appbundle_password"]', this.sendPasswordForm);
        },

        beforeDestroy() {
            $(document).off('submit', 'form[name="appbundle_password"]', this.sendPasswordForm)
        }
    }
</script>