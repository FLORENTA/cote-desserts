<template>
    <div>
        <h2 v-if="nbArticles > 0">{{ nbArticles }} article(s)</h2>
        <h2 v-else>{{ t('admin.homepage.no_article') }}</h2>
        <div class="container_flex">
            <div class="tile" style='margin: 30px 0;' v-for="(article, key) in articles">
                <h3 class="text_centered">{{ article.title }}</h3>
                <router-link v-bind:to="{name: 'adminEditArticle', params: {token: article.token} }">
                    <div class='image' v-bind:style="{
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        height: '225px',
                        backgroundImage: 'url(' + getImage(article.image_src) + ')'
                     }" v-bind:alt="article.slug">
                    </div>
                </router-link>
                <i class="fa fa-comment number-comments" v-bind:id="'number-of-comments-'.concat(article.token)" v-on:click="fetchArticleComments(article.id)"> {{ article.number_comments }}</i>
                <div class="button-group">
                    <router-link v-bind:to="{name: 'adminEditArticle', params: {token: article.token}}">
                        <button class="button-default">
                            <i class="fa fa-edit"></i>&nbsp;&nbsp;
                            {{ t('admin.homepage.button.edit') }}
                        </button>
                    </router-link>
                    <button class="button-delete" v-on:click="deleteArticle(article.token, key)" v-bind:data-token="article.token">
                        <i class="fa fa-trash"></i>
                        {{ t('admin.homepage.button.delete')}}
                    </button>
                </div>
            </div>
        </div>
        <transition name="fade">
            <div id="results-modal" v-show="displayResultModal">
                <h3 class="text-centered">{{ t('comment.title') }}</h3>
                <div class="lead" id="results">
                    <div class="container" v-if="undefined !== articleComments && articleComments.length > 0">
                        <p><span id="number-of-comments">{{ articleComments.length }}</span> {{ t('admin.homepage.comments') }}</p>
                        <section class="article-comment comment" v-for="comment in articleComments">
                            {{ t('admin.homepage.comments.email') }} {{ comment.email }} <br>
                            {{ comment.comment }}
                            <div class="button-group">
                                <button :class="'update-comment-status '.concat(getClass(comment.published))" :data-publish="comment.published ? 'true' : 'false'" :data-token="comment.token">
                                    {{ comment.published ? t('comment.status.hide') : t('comment.status.publish') }}
                                </button>
                                <button class="button-delete delete-comment" :data-token="comment.token" :data-article-token="comment.article_token">{{ t('comment.button.delete') }}</button>
                            </div>
                        </section>
                    </div>
                    <div v-else>
                        {{ t('query.no_comment') }}
                    </div>
                </div>
                <i class="fa fa-times close-button"></i>
            </div>
        </transition>
    </div>
</template>

<script>
    import {Routing} from './../../js/routing';
    import {addAlert} from "./../../js/alert";
    import {spinner} from "../../mixins/spinner";

    export default {
        name: 'admin-articles',

        data() {
            return {
                displayResultModal: false,
                articles: this.$root.$data.articles,
                articleComments: undefined
            }
        },

        computed : {
            nbArticles() {
                return this.articles.length;
            }
        },

        mixins: [spinner],

        methods: {
            getClass(published) {
                return published ? 'button-delete' : 'button-default';
            },

            fetchArticleComments(articleId) {
                $.get(Routing.generate('get_article_comments', { id: articleId }), response => {
                    this.articleComments = response;
                    this.displayResultModal = true;
                }).fail(err => {
                    addAlert(err.responseJSON);
                });
            },

            deleteArticle(token, key) {
                if (confirm(Translator.trans('admin.homepage.article.delete.confirm'))) {
                    $.ajax({
                        type: 'DELETE',
                        url: Routing.generate('delete_article', { token: token }),
                        success : data => {
                            addAlert(data);
                            this.$store.state.articles.splice(key, 1)
                        },
                        error : err => {
                            addAlert(err);
                        }
                    });
                }
            },

            deleteComment(e) {
                let $target = $(e.target);
                let $token = $target.data('token');
                let $articleToken = $target.data('article-token');

                $.ajax({
                    type: 'DELETE',
                    url: Routing.generate('delete_comment', {token: $token}),
                    success : response => {
                        $target.parents('section').remove();
                        addAlert(response);
                        let $numberOfCommentsNode = $('#number-of-comments');
                        let $numberOfComments = Number($numberOfCommentsNode.text());
                        $numberOfComments--;
                        $numberOfCommentsNode.text($numberOfComments);
                        $('#number-of-comments-' + $articleToken).text(' ' + $numberOfComments);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    }
                });
            },

            updateCommentStatus(e) {
                let $target = $(e.target);
                let $token = $target.data('token');
                let $publish = !$target.data('publish');

                $.ajax({
                    type: 'PUT',
                    url: Routing.generate('change_comment_status', {status: '' + $publish + '', token: $token}),
                    success: response => {
                        $target.data('publish', $publish);
                        $publish
                            ? $target.addClass('button-delete').removeClass('button-default')
                            : $target.addClass('button-default').removeClass('button-delete');
                        $target.text(Translator.trans($publish ? 'comment.status.hide' : 'comment.status.publish'));
                        addAlert(response);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    }
                });
            },

            getImage(src) {
                return window.location.origin + './images/' + src;
            }
        },

        created() {
            this.launchSpinnerAnimation();
        },

        mounted() {
            $(document).on('click', '.close-button', () => {
                this.displayResultModal = false;
            });

            $(document).on('click', '.delete-comment', this.deleteComment);
            $(document).on('click', '.update-comment-status', this.updateCommentStatus);
        },

        beforeDestroy() {
            $(document).off('click', '.delete-comment', this.deleteComment);
            $(document).off('click', '.update-comment-status', this.updateCommentStatus);
        }
    }
</script>

<style scoped>
    @media all and (min-width: 1024px) {
        .tile {
            width: 30%!important;
        }
    }
</style>