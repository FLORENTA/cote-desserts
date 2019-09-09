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
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import {addAlert} from "../../js/alert";
    import {NAVIGATION_TYPE} from "../../js/variables";
    import {spinner} from "../../mixins/spinner";

    export default {
        name: 'user-homepage',

        computed : {
            ...mapState({
                articles: state => state.articles
            })
        },

        mixins: [spinner],

        created() {
            this.$store.dispatch('newStatistic', {
                data: this.$route.fullPath,
                type: NAVIGATION_TYPE
            });
        },

        mounted() {
            this.$store.dispatch('getArticles').catch(err => {
                addAlert(err);
            }).finally(() => {
                this.cancelSpinnerAnimation();
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