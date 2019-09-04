<template>
    <div>
        <transition-group name="fade" class="container_flex">
            <div v-for='article in articles' v-bind:key="article.id" class="tile">
                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <div class='image' v-bind:style="{
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        height: '225px',
                        backgroundImage: 'url('+'./images/' + article.image_src +')'
                     }" v-bind:alt="article.slug">
                    </div>
                </router-link>
                <h2>{{ article.title }}</h2>
                <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                    <button class="button-default m10">{{ t('article.access_button') }}</button>
                </router-link>
            </div>
        </transition-group>

        <div v-if="articles.length > 0" style="margin-bottom: 50px;">
            <button v-if="articlesCount > nbArticles" v-on:click="addArticles" class="button-submit">
                {{ t('homepage.more_articles') }}
            </button>
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import {addAlert} from "../../js/alert";
    import {NAVIGATION_TYPE} from "../../js/variables";
    import {Spinner} from "../../mixins/spinner";

    export default {
        name: 'user-homepage',

        computed : {
            ...mapState({
                articles: state => state.articles,
                nbArticles: state => state.articles.length,
                articlesCount: state => state.articlesCount,
            })
        },

        mixins: [Spinner],

        methods: {
            getArticles(add = false) {
                this.launchSpinnerAnimation();
                this.$store.dispatch('getArticles', add).catch(err => {
                    addAlert(err);
                }).finally(() => {
                    this.cancelSpinnerAnimation();
                });
            },

            addArticles() {
                this.getArticles(true);
            },
        },

        created() {
            this.$store.dispatch('newStatistic', {
                data: this.$route.fullPath,
                type: NAVIGATION_TYPE
            });
        },

        mounted() {
            let maxId = $('.js-app').data('max-id');
            this.$store.commit('storeMaxArticleId', maxId);
            this.getArticles();
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