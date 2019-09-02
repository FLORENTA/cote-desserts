<template>
    <transition name="fade">
        <div class="container w40 w95sm" id="legal-form-container" v-show="isLegalMentionsLoaded"></div>
    </transition>
</template>

<script>
    import Mixins from '../../mixins';
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";

    export default {
        name: 'admin-legal',

        data() {
            return {
                content: '',
                toUpdate: false,
                isLegalMentionsLoaded: false
            }
        },

        methods: {
            handleLegalFormSubmission(e) {
                e.preventDefault();
                let $form = $(e.target)[0];
                let formData = new FormData($form);

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
                    }
                });
            }
        },

        mixins: [Mixins],

        mounted() {
            // Fetch legal mentions form
            $.ajax({
                type: 'GET',
                url: Routing.generate('fetch_create_legal_form'),
                success: response => {
                    $('#legal-form-container').append(response);
                    this.isLegalMentionsLoaded = true;
                },
                error: err => {
                    addAlert(err.responseJSON);
                }
            });

            $(document).on('submit', 'form[name="appbundle_legal"]', this.handleLegalFormSubmission);
        },

        beforeDestroy() {
            $(document).off('submit', 'form[name="appbundle_legal"]', this.handleLegalFormSubmission);
        }
    }
</script>