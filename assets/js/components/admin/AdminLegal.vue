<template>
    <transition name="fade">
        <div class="container w40 w95sm" id="legal-form-container" v-show="isLegalMentionsLoaded"></div>
    </transition>
</template>

<script>
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";
    import {spinner} from "../../mixins/spinner";

    export default {
        name: 'admin-legal',

        data() {
            return {
                content: '',
                toUpdate: false,
                isLegalMentionsLoaded: false
            }
        },

        mixins: [spinner],

        methods: {
            handleLegalFormSubmission(e) {
                e.preventDefault();
                let $form = $(e.target)[0];
                let formData = new FormData($form);
                let $submitButton = $('#appbundle_legal_submit');
                this.addButtonLoader($submitButton);

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: response => {
                        addAlert(response);
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
            // Fetch legal mentions form
            $.get(Routing.generate('get_legal_create_form'), response => {
                $('#legal-form-container').append(response);
                this.isLegalMentionsLoaded = true;
            }).fail(err => {
                addAlert(err.responseJSON);
            });

            $(document).on('submit', 'form[name="appbundle_legal"]', this.handleLegalFormSubmission);
        },

        beforeDestroy() {
            $(document).off('submit', 'form[name="appbundle_legal"]', this.handleLegalFormSubmission);
        }
    }
</script>