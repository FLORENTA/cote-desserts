import Vue from 'vue'
import Vuex from 'vuex';
import {Routing} from './../js/routing';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        articles: [],
        ids: [],
        articlesCount: 0,
        lastId: undefined,
        displayMessage: false,
        message: '',
        timer: undefined
    },

    mutations: {
        // Function to start the max article id in db when home-page loads
        storeMaxArticleId(state, maxId) {
            state.lastId = maxId;
        },

        addArticles(state, data) {
            let $container = $('.container_flex');

            // new article added to the list (not at initialization)
            if ('add' in data) {
                if (true === data['add']) {
                    $('html').animate({
                        scrollTop: ($container.position().top + $container.height())
                    }, 500);
                }
            }

            // data['response'] contains all the next range found articles
            if ('response' in data) {
                data['response'].forEach((article, index, array) => {
                    /* Getting the id of the last article in the array for the next request */
                    let articleId = article.id;
                    if (index === array.length - 1) {
                        state.lastId = articleId - 1;
                    }
                    // If the article has already been rendered, do not render again
                    // This event already occurs when clicking on the previous page
                    // button to go back to homepage
                    if (state.ids.indexOf(articleId) === -1) {
                        state.ids.push(articleId);
                        state.articles.push(article);
                    }
                });
            }
        },

        setNumberOfArticles(state, data) {
            state.articlesCount = data;
        },

        displayServerMessage(state, message) {
            /* if a message is already being displayed */
            /* Reset the timer */
            if (state.displayMessage) clearTimeout(state.timer);
            state.displayMessage = true;
            state.message = message;
            state.timer = setTimeout(() => {
                state.displayMessage = !state.displayMessage;
            }, 3000);
        },

        displaySendingRequest() {
            this.commit('displayServerMessage', Translator.trans('store.request.sending'));
        },

        displayWaitingForData() {
            this.commit('displayServerMessage', Translator.trans('store.request.receiving'));
        },

        hideMessage(state) {
            state.displayMessage = false;
            clearTimeout(state.timer);
        }
    },

    actions: {
        getArticles(context, add) {
            let id = this.state.lastId;
            return new Promise((resolve, reject) => {
                if (this.state.lastId !== 0) {
                    $.get(Routing.generate('fetch_articles_by_id', { id: id }), response => {
                        context.commit('hideMessage');
                        if (response.length > 0) {
                            context.commit('addArticles', {
                                response: response, add: add
                            });
                        }
                        resolve();
                    }).fail(err => {
                        reject(err.responseJSON);
                    });
                } else {
                    reject(Translator.trans('query.no_article'));
                }
            });
        },

        getNumberOfArticles(context) {
            $.get(Routing.generate('articles_count'), response => {
                context.commit('setNumberOfArticles', response);
            }).fail(() => {
                context.commit('setNumberOfArticles', 0);
            });
        },

        newStatistic(context, data) {
            $.post(Routing.generate('statistic_new'), {
               data: data.data,
               type: data.type
            });
        },
    }
});