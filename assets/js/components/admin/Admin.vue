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
    import Mixins from './../../mixins/index';
    import MenuMixins from './../../mixins/menuMixin';
    import { mapState } from 'vuex';
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome';
    import faBars from '@fortawesome/fontawesome-free-solid/faBars';
    import ServerMessage from "./../ServerMessage";
    import {addAlert} from "../../js/alert";
    import {Routing} from './../../js/routing';

    export default {
        name: 'admin',

        data() {
            return {
                barsIcon: faBars,
                categories: undefined
            }
        },

        mixins: [Mixins, MenuMixins],

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
        },

        mounted() {
            // Cannot be put into article_form.js due to event handlers set multiple times
            // when navigating from page to page
            $(document).on('keyup', '.input-category', e => {
                let $target = $(e.target);
                $target.next('.category-suggestion').remove();

                if ($target.val().length === 0) {
                    return;
                }

                let $suggestions = $('<ul class="category-suggestion"></ul>');

                let $matches = $.grep(this.categories, n => {
                    let $val = $target.val();
                    return n.slice(0, $val.length) === $val.charAt(0).toUpperCase() + $val.slice(1) && n.length !== $val.length;
                });

                if ($matches.length > 0) {
                    $matches.forEach($match => {
                        let $word = $('<li>'+ $match +'</li>');
                        $suggestions.append($word);
                        $word.click(function() {
                            $suggestions.prev().val($(this).text().trim());
                            $(this).parent().remove();
                        });
                    });

                    $target.after($suggestions);
                }
            });

            $(document).on('submit', 'form[name="appbundle_article"]', e => {
                e.preventDefault();
                let $form = $(e.target)[0];
                let $submitButton = $('#appbundle_article_submit');
                $submitButton.append($("<span>&nbsp;<i class='fa fa-spinner fa-spin'></i><span>"));

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    processData: false,
                    contentType: false,
                    data: new FormData($form),
                    success: response => {
                        addAlert(response);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    },
                    complete() {
                        $submitButton.find('span').remove();
                    }
                });
            });

            $(document).on('submit', 'form[name="appbundle_password"]', e => {
                e.preventDefault();
                let $form = $(e.target)[0];
                let formData = new FormData($form);
                let $submitButton = $('#appbundle_password_submit');
                $submitButton.append(
                    $("<span>&nbsp;<i class='fa fa-spinner fa-spin'></i><span>")
                );

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: response => {
                        addAlert(response);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    },
                    complete: () => {
                        $submitButton.find('span').remove();
                    }
                })
            });

            $(document).on('click', '#remove-pdf', e => {
                e.preventDefault();
                this.$root.$emit('deletePdf', { detail: $(e.currentTarget).data('pdf') });
            });
        }
    }
</script>

