<template>
    <transition name="fade">
        <div class="container w40 w95sm" id="edit-article-form-container" v-show="isEditArticleFormLoaded">
            <h2>{{ t('admin.article.edit.title') }}</h2>
        </div>
    </transition>
</template>

<script>
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";

    export default {
        name: 'admin-edit-article',

        data() {
            return {
                token: this.$route.params.token,
                isEditArticleFormLoaded: false
            }
        },

        methods: {
            deletePDF(pdf) {
                $.ajax({
                    type: 'DELETE',
                    url: Routing.generate('delete_pdf', { pdf: pdf }),
                    data: {pdf: this.pdf},
                    success: response => {
                        addAlert(response);
                        this.pdf = undefined;
                    },
                    error : err => {
                        addAlert(err.responseJSON);
                    }
                });
            },

            handleSubmit(e) {
                let formData = new FormData($(e.target)[0]);
                formData.append('sender', 'create_edit_article');

                $.ajax({
                    type: 'POST',
                    url: '/admin/articles/edit/' + this.$route.params.token,
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: response => {
                        addAlert(response)
                    },
                    error: err => {
                        addAlert(err.responseJSON)
                    }
                })
            }
        },

        mounted() {
            $.get(Routing.generate('fetch_edit_article_form', { token : this.$route.params.token }), response => {
                $('#edit-article-form-container').append(response);
                this.isEditArticleFormLoaded = true;
            });

            this.$root.$on('deletePdf', e => {
                this.deletePDF(e.detail);
            });
        }
    }
</script>