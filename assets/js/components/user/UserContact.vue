<template>
    <div class="container w40 w95sm" id="contact-form-container" v-show="loaded"></div>
</template>

<script>
    import {Routing} from './../../js/routing';
    import {NAVIGATION_TYPE} from "../../js/variables";
    import {addAlert} from "../../js/alert";
    import {spinner} from "../../mixins/spinner";

    export default {
        name: 'user-contact',

        data() {
            return {
                loaded: false
            }
        },

        mixins: [spinner],

        created() {
            this.$store.dispatch('newStatistic', {
                data: this.$route.fullPath,
                type: NAVIGATION_TYPE
            });
        },

        methods: {
            handleContactFormSubmission(e) {
                e.preventDefault();
                let $form = $(e.target)[0];
                let formData = new FormData($form);
                let $submitButton = $('#appbundle_contact_submit');

                this.addButtonLoader($submitButton);

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: response => {
                        addAlert(response);
                        $form.reset();
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
            this.launchSpinnerAnimation();

            $.get(Routing.generate('fetch_contact_form'), response => {
                $('#contact-form-container').append(response);
                this.cancelSpinnerAnimation();
                this.loaded = true;
            });

            $(document).on('submit', 'form[name="appbundle_contact"]', this.handleContactFormSubmission);
        },

        beforeDestroy() {
            $(document).off('submit', 'form[name="appbundle_contact"]', this.handleContactFormSubmission);
        }
    }
</script>