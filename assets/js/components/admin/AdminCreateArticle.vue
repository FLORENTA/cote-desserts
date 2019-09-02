<template>
    <transition name="fade">
        <div class="container w40 w95sm" id="create-article-form-container" v-show="isCreateArticleFormLoaded">
            <h2>{{ t('admin.article.create.title') }}</h2>
        </div>
    </transition>
</template>

<script>
    import {Routing} from './../../js/routing';
    import Mixins from "../../mixins";

    export default {
        name: 'admin-create-article',

        data() {
            return {
                isCreateArticleFormLoaded: false
            }
        },

        mixins: [Mixins],

        mounted() {
            $.get(Routing.generate('fetch_create_article_form'), response => {
                $('#create-article-form-container').append(response);
                this.isCreateArticleFormLoaded = true;
            });

            $(document).on('keyup', '.input-category', this.handleInputCategory);
            $(document).on('submit', 'form[name="appbundle_article"]', this.handleArticleFormSubmission);
        },

        beforeDestroy() {
            $(document).off('keyup', '.input-category', this.handleInputCategory);
            $(document).off('submit', 'form[name="appbundle_article"]', this.handleArticleFormSubmission);
        }
    }
</script>