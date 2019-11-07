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
    import {article} from "../../mixins/article";

    export default {
        name: 'admin-edit-article',

        data() {
            return {
                token: this.$route.params.token,
                isEditArticleFormLoaded: false
            }
        },

        methods: {
            deletePDF(e) {
                e.preventDefault();
                let $button = $(e.target);
                $.ajax({
                    type: 'DELETE',
                    url: $button.data('delete-pdf-url'),
                    success: response => {
                        addAlert(response);
                        $button.prev('p').remove();
                        $button.remove();
                    },
                    error : err => {
                        addAlert(err.responseJSON);
                    }
                });
            }
        },

        mixins: [article],

        mounted() {
            $.get(Routing.generate('get_article_edit_form', { token : this.$route.params.token }), response => {
                $('#edit-article-form-container').append(response);
                this.isEditArticleFormLoaded = true;
            });

            $(document).on('click', '#remove-pdf', this.deletePDF);
            $(document).on('keyup', '.input-category', this.handleInputCategory);
            $(document).on('submit', 'form[name="appbundle_article"]', this.handleArticleFormSubmission);
        },

        beforeDestroy() {
            $(document).off('click', '#remove-pdf', this.deletePDF);
            $(document).off('keyup', '.input-category', this.handleInputCategory);
            $(document).off('submit', 'form[name="appbundle_article"]', this.handleArticleFormSubmission);
        }
    }
</script>