import Vue from 'vue'
import Vuex from 'vuex';
import {Routing} from './../js/routing';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        articles: [],
        displayMessage: false,
        message: undefined,
        timer: undefined
    },

    mutations: {
        setArticles(state, articles) {
            state.articles = articles;
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
        getArticles(context) {
            return new Promise((resolve, reject) => {
                $.get(Routing.generate('fetch_articles'), response => {
                    context.commit('hideMessage');
                    context.commit('setArticles', response);
                    resolve();
                }).fail(err => {
                    reject(err.responseJSON);
                });
            });
        },

        newStatistic(context, data) {
            let formData = new FormData();
            formData.append('data', data.data);
            formData.append('type', data.type);

            navigator.sendBeacon(Routing.generate('statistic_new'), formData);
        }
    }
});