<template>
    <div id="app_user">
        <nav>
            <div id="menu-icons" v-if='smallDevice' v-on:click="showMenu">
                <i class="fa fa-bars fa-lg" v-if="!isMenuDisplayed"></i>
                <i class="fa fa-remove fa-lg" v-if="isMenuDisplayed"></i>
            </div>
            <ul id="menu" v-on:click="resetMenu()">
                <router-link v-bind:to="{name: 'home_user'}" tag="li" >{{ t('menu.home') }}</router-link>
                <router-link v-bind:to="{name: 'categories'}" tag="li" >{{ t('menu.categories') }}</router-link>
                <router-link v-bind:to="{name: 'contact'}" tag="li">{{ t('menu.contact') }}</router-link>
            </ul>
            <transition name="fade">
                <div id="search-form-container" v-show="isSearchFormLoaded"></div>
            </transition>
        </nav>
        <router-view></router-view>
        <transition name="fade">
            <div id="results-modal" v-show="displayResultModal">
                <h3 class="text-centered">{{ t('search.suggestions') }}</h3>
                <p class="lead" id="results"></p>
                <i class="fa fa-times close-button"></i>
            </div>
        </transition>
        <div id="loader-container" v-show="loading">
            <div id="loader">
                <i class="fa fa-spinner fa-2x loading-page-spinner"></i>
            </div>
        </div>
        <footer>
            <transition name="fade">
                <div id="newsletter-form-container" v-show="isNewsletterFormLoaded"></div>
            </transition>
            <div id="legal_mentions">
                &copy; {{ t('html.footer.copyright') }} |  &nbsp;<router-link v-bind:to="{name: 'user-legal'}">{{ t('html.footer.legal_mentions') }}</router-link>
            </div>
        </footer>
        <server-message v-bind:displayMessage="displayMessage">{{ message }}</server-message>
    </div>
</template>

<script>
    import Mixins from '../../mixins';
    import MenuMixin from '../../mixins/menuMixin';
    import {mapState} from 'vuex';
    import ServerMessage from "../ServerMessage";
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";

    export default {
        name: 'user',

        data() {
            return {
                showSuggestionList: false,
                showSpinner: false,
                isSearchFormLoaded: false,
                isNewsletterFormLoaded: false,
                newsletter: undefined,
                newsletterFormValid: false,
                loading: false,
                displayResultModal: false
            }
        },

        computed: {
            ...mapState({
                displayMessage: state => state.displayMessage,
                message: state => state.message,
            })
        },

        mixins: [Mixins, MenuMixin],

        components: {
            ServerMessage
        },

        methods: {
            addButtonLoader($button) {
                if (!$button.hasClass('fa-spinner')) {
                    $button.append(
                        $("<span>&nbsp;<i class='fa fa-spinner fa-spin'></i><span>")
                    );
                }
            },

            removeButtonLoader($button) {
                $button.find('span').remove();
            }
        },

        mounted() {
            // Get the search form
            $.get(Routing.generate('fetch_search_form'), response => {
                $('#search-form-container').append(response);
                this.isSearchFormLoaded = true;
            });

            // Get the newsletter form
            $.get(Routing.generate('fetch_newsletter_form'), response => {
                $('#newsletter-form-container').append(response);
                this.isNewsletterFormLoaded = true;
            });

            // Handle submission of the newsletter form
            $(document).on('submit', '.newsletter-form', e => {
                e.preventDefault();
                let formData = new FormData($(e.target)[0]);
                let $submitButton = $('#appbundle_newsletter_submit');

                this.addButtonLoader($submitButton);

                $.ajax({
                    type: 'POST',
                    url: Routing.generate('newsletter_new'),
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: response => {
                        addAlert(response);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    },
                    complete: () => {
                        this.removeButtonLoader($submitButton);
                    }
                });
            });

            // Contact component
            $(document).on('submit', 'form[name="appbundle_contact"]', e => {
                e.preventDefault();
                let $form = $(e.target)[0];
                let formData = new FormData($form);

                let $submitButton = $('#appbundle_contact_submit');

                this.addButtonLoader($submitButton);

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: response => {
                        addAlert(response);
                        $form.reset();
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    },
                    complete: () => {
                        this.removeButtonLoader($submitButton);
                    }
                });
            });

            // Category component
            $(document).on('submit', 'form[name="appbundle_category"]', e => {
                e.preventDefault();
                let $form = $(e.target)[0];
                let formData = new FormData($form);

                let $submitButton = $('#appbundle_category_submit');

                this.addButtonLoader($submitButton);

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: response => {
                        this.$root.$emit('fetch_article_by_category_result', { detail: response} );
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    },
                    complete() {
                        this.removeButtonLoader($submitButton);
                    }
                });
            });

            // Comment modal in article component
            $(document).on('submit', 'form[name="appbundle_comment"]', e => {
                e.preventDefault();
                let $submitButton = $('#appbundle_comment_submit');

                this.addButtonLoader($submitButton);

                let $form = $(e.target)[0];
                let formData = new FormData($form);

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: response => {
                        addAlert(response);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    },
                    complete: () => {
                        this.$root.$emit('hide-comment-modal');
                        this.removeButtonLoader($submitButton);
                    }
                });
            });

            $(window).on('router-push', e => {
                this.$router.push({
                    name: 'article',
                    params: {
                        slug: e.detail
                    }
                });

                this.$router.go(0);
            });

            $(window).on('display-results-modal', () => {
                this.displayResultModal = true;
            });

            $(window).on('hide-results-modal', () => {
                this.displayResultModal = false;
            });

            $(document).on('click', '.close-button', () => {
                this.displayResultModal = false;
            });

            $(document).on('click', () => {
                if (this.displayResultModal) {
                    this.displayResultModal = false;
                }
            });

            $(document).on('click', '#close-comment-modal-button', () => {
                this.$root.$emit('hide-comment-modal');
            });

            $(document).on('contextmenu', 'img', () => {
                return false;
            });
        }
    }
</script>