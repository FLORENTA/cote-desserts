<template>
    <div>
        <div class="container w40 w95sm" v-if="newsletters">
            <b>{{ newsletters.length }} {{ t('admin.newsletter.subscribers') }}</b>
        </div>
        <transition name="fade">
            <div class="container w40 w95sm" v-if="newsletters">
                <div class="tile">
                    <table id="newsletter-table" class="table">
                        <thead>
                            <tr>
                                <th>{{ t('admin.newsletter.table.head.email') }}</th>
                                <th>{{ t('admin.newsletter.table.head.subscription_date') }}</th>
                                <th>{{ t('admin.newsletter.table.head.action') }}</th>
                            </tr>
                        </thead>
                        <tbody v-for="newsletter in newsletters">
                            <tr>
                                <td>{{ newsletter.email }}</td>
                                <td>{{ newsletter.date|formatShortDate }} </td>
                                <td>
                                    <button class="button-delete" v-bind:data-token='newsletter.token' v-on:click='unsubscribe'>
                                        <i class="fa fa-trash"></i> {{ t('admin.newsletter.button.delete')}}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </transition>
        <transition name="fade">
            <div class="container w40 w95sm" v-if="articlesWithNewsletter">
                <div class="tile">
                    <b>{{ t('admin.newsletter.sent_newsletters') }}</b>
                    <template v-for="articleWithNewsletter in articlesWithNewsletter">
                        <p>{{ articleWithNewsletter.title }}</p>
                    </template>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
    import {Routing} from './../../js/routing';
    import Mixins from './../../mixins';
    import {addAlert, hideMessage} from "../../js/alert";

    export default {
        name: 'admin-newsletter',

        data() {
            return {
                newsletters: undefined,
                articlesWithNewsletter: undefined
            }
        },

        mixins: [Mixins],

        methods: {
            getNewsletters() {
                $.get(Routing.generate('admin_newsletters'), response => {
                    this.newsletters = response;
                    hideMessage();
                }).fail(err => {
                    addAlert(err.responseJSON);
                });
            },

            getArticlesWithNewsletter() {
                $.get(Routing.generate('fetch_articles_with_newsletter'), response => {
                    this.articlesWithNewsletter = response;
                });
            },

            unsubscribe(e) {
                $.post(Routing.generate('admin_unsubscribe', { token: $(e.target).data('token') }), response => {
                    addAlert(response);
                    $(e.target).parents('tr').remove();
                }).fail(err => {
                    addAlert(err.responseJSON);
                });
            }
        },

        created() {
            this.$store.commit('displayWaitingForData');
            this.getNewsletters();
            this.getArticlesWithNewsletter();
        }
    }
</script>