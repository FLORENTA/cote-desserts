<template>
    <transition name="fade">
        <div class="container w40 w95sm" id="create-article-form-container" v-show="isCreateArticleFormLoaded">
            <h2>{{ t('admin.article.create.title') }}</h2>
        </div>
    </transition>
</template>

<script>
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";

    export default {
        name: 'admin-create-article',

        data() {
            return {
                isCreateArticleFormLoaded: false
            }
        },

        methods: {
            handleSubmit(e) {
                let $form = $(e.target)[0];
                let formData = new FormData($form);

                $.ajax({
                    type: 'POST',
                    url: $form.action,
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
            $.get(Routing.generate('fetch_create_article_form'), response => {
                $('#create-article-form-container').append(response);
                this.isCreateArticleFormLoaded = true;
            });
        }
    }
</script>