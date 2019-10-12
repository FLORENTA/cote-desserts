<template>
    <transition name="fade">
        <div class="container w40 w95sm" id="link-form-container" v-show="isLinkFormLoaded"></div>
    </transition>
</template>

<script>
    import {Routing} from './../../js/routing';

    export default {
        name: 'admin-link',

        data() {
            return {
                isLinkFormLoaded: false
            }
        },

        created() {
            $.get(Routing.generate('fetch_link_form'), response => {
                $('#link-form-container').append(response);
                this.isLinkFormLoaded = true;
            });
        },

        mounted() {
            $(document).on('submit', 'form[name="appbundle_link"]', e => {
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

                    },
                    error: err => {

                    },
                    complete: () => {

                    }
                });
            });
        }
    }
</script>