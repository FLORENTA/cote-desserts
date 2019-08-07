import Vue from 'vue';
import {Router} from './router';
import { store } from "./store";
import VueTranslate from 'vue-translate-plugin';

const t = require('./../../web/js/translations/messages/fr.json');

// Enable translations in all components
Vue.use(VueTranslate);

$(document).ready(() => {
    new Vue({
        el: "#app",
        router: Router,
        store: store,
        locales: {
            fr: t.translations.fr.messages
        },
        mounted() {
            this.$translate.setLang(Translator.locale);
        }
    });
});

