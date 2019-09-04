<template>
    <div>
        <nav id="admin_navbar">
            <div id="burger" v-if='smallDevice' v-on:click="showMenu">
                <font-awesome-icon style="color: #fff;" v-bind:icon="barsIcon" size="lg"/>
            </div>
            <ul id="menu" v-on:click="resetMenu()"> <!-- menu not used in css but in javascript -->
                <router-link v-bind:to="{name: 'createArticle'}" tag="li">{{ t('admin.menu.article.create') }}</router-link>
                <router-link v-bind:to="{name: 'homepageAdmin'}" tag="li">{{ t('admin.menu.article.list') }}</router-link>
                <router-link v-bind:to="{name: 'comments'}" tag="li">{{ t('admin.menu.comments') }}</router-link>
                <router-link v-bind:to="{name: 'newsletter'}" tag="li">{{ t('admin.menu.newsletter') }}</router-link>
                <router-link v-bind:to="{name: 'password'}" tag="li">{{ t('admin.menu.password') }}</router-link>
                <router-link v-bind:to="{name: 'statistic'}" tag="li">{{ t('admin.menu.statistics') }}</router-link>
                <router-link v-bind:to="{name: 'contacts'}" tag="li">{{ t('admin.menu.contacts') }}</router-link>
                <router-link v-bind:to="{name: 'admin-legal'}" tag="li">{{ t('admin.menu.legal') }}</router-link>
                <router-link v-bind:to="{name: 'logout'}" tag="li">{{ t('admin.menu.logout') }}</router-link>
            </ul>
        </nav>
        <router-view></router-view>
        <server-message v-bind:displayMessage="displayMessage">{{ message }}</server-message>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faBars from '@fortawesome/fontawesome-free-solid/faBars';
    import ServerMessage from "./../ServerMessage";
    import {Routing} from './../../js/routing';
    import {menu} from "../../mixins/menu";

    export default {
        name: 'admin',

        data() {
            return {
                barsIcon: faBars,
                categories: undefined
            }
        },

        mixins: [menu],

        computed: mapState(['displayMessage', 'message']),

        components: {
            ServerMessage,
            FontAwesomeIcon
        },

        beforeRouteUpdate(to, from, next) {
            if (to.name === 'logout') {
                localStorage.removeItem('token');
                next('/login')
            } else {
                next();
            }
        },

        created() {
            $.get(Routing.generate('fetch_categories'), response => {
                this.categories = response;
            });
        }
    }
</script>

