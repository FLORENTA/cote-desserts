<template>
    <div>
        <h2 v-if="nbArticles > 0">{{ nbArticles }} article(s)</h2>
        <h2 v-else>{{ t('admin.homepage.no_article') }}</h2>
        <div class="container_flex">
            <div class="tile" style='margin: 30px 0;' v-for="(article, key) in articles">
                <h3 class="text">{{ article.title }}</h3>
                <img class='image' v-bind:src="'./images/' + article.image_src"/>
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

        computed : mapState({
            articles : state => state.articles,
            nbArticles : state => state.articles.length,
            articlesCount : state => state.articlesCount,
        }),

        mixins: [Mixin],

        methods: {
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