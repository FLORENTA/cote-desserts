<template>
    <div>
        <h2 v-if="nbArticles > 0">{{ nbArticles }} article(s)</h2>
        <h2 v-else>{{ t('admin.homepage.no_article') }}</h2>
        <div class="container_flex">
            <div class="tile" style='margin: 30px 0;' v-for="(article, key) in articles">
                <h3 class="text_centered">{{ article.title }}</h3>
                <img class='image' v-bind:src="'./images/' + article.image_src"/>
                <i class="fa fa-comment number-comments" v-bind:id="'number-of-comments-'.concat(article.token)" v-on:click="fetchArticleComments(article.id)"> {{ article.number_comments }}</i>
                <div class="button-group">
                    <router-link v-bind:to="{name: 'editArticle', params: {token: article.token}}">
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
                <p class="lead" id="results"></p>
                <i class="fa fa-times close-button"></i>
            </div>
        </transition>

        <button v-if="articlesCount > nbArticles && nbArticles !== 0" v-on:click="addArticles" class="button-default m10"><i class="fa fa-plus-circle"></i> {{ t('admin.homepage.button.more_articles')}}</button>
    </div>
</template>

<script>
    import Mixin from './../../mixins';
    import { mapState } from 'vuex';
    import {Routing} from './../../js/routing';
    import {addAlert} from "./../../js/alert";

    export default {
        name: 'admin-homepage',

        data() {
            return {
                displayResultModal: false
            }
        },

        computed : mapState({
            articles : state => state.articles,
            nbArticles : state => state.articles.length,
            articlesCount : state => state.articlesCount,
        }),

        mixins: [Mixin],

        methods: {
            fetchArticleComments(articleId) {
                $.get(Routing.generate('fetch_comments_by_article', { id: articleId }), response => {
                    $('#results').empty().append(response);
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

            addArticles() {
                this.$store.dispatch('getArticles').catch(err => {
                    addAlert(err);
                }).finally(() => {
                    this.cancelSpinnerAnimation();
                });
            }
        },

        mounted() {
            let maxId = $('.js-app').data('max-id');
            this.$store.commit('storeMaxArticleId', maxId);
            this.$store.commit('displayWaitingForData');

            this.$store.dispatch('getArticles').catch(err => {
                addAlert(err);
            }).finally(() => {
                this.cancelSpinnerAnimation();
            });

            this.$store.dispatch('getNumberOfArticles');

            $(document).on('click', '.close-button', () => {
                this.displayResultModal = false;
            });

            $(document).on('click', '.delete-comment', e => {
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
            });

            $(document).on('click', '.update-comment-status', e => {
                let $target = $(e.target);
                let $token = $target.data('token');
                console.log($target.data('publish'));
                let $publish = !$target.data('publish');
                console.log($publish);

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
            });
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