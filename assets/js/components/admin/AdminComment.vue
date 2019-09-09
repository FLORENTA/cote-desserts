<template>
    <div>
        <h2 v-if="comments">{{ comments.length }} {{ t('comment.title') }}</h2>
        <div class="container w40 w95sm" v-if="comments">
            <div class="tile" v-for="(comment, key) in comments">
                <p v-bind:style="style">{{ comment.username }}, le {{ comment.date|formatShortDate }}</p>
                <p>{{ comment.email }}</p>
                <p>{{ comment.comment }}</p>
                <p v-if="null !== comment.article" v-bind:style="style">Article: {{ comment.article.title }}</p>
                <div class="button-group">
                    <button v-if="comment.published" v-on:click='updateStatus(false, comment.token, key)' class="button-delete">{{ t('comment.status.hide')}}</button>
                    <button v-else v-on:click='updateStatus(true, comment.token, key)' class="button-default">{{ t('comment.status.publish') }}</button>
                    <button class="button-delete" v-on:click="deleteComment(comment.token, key)">
                        <i class="fa fa-trash"></i>
                        {{ t('comment.button.delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {Routing} from './../../js/routing';
    import {addAlert} from "./../../js/alert";
    import {date} from "../../mixins/date";

    export default {
        name: 'admin-comment',

        data() {
            return {
                comments: undefined
            }
        },

        mixins: [date],

        computed: {
            style() {
                return {
                    borderBottom: '1px solid lightgrey',
                }
            }
        },

        methods: {
            getComments() {
                $.get(Routing.generate('fetch_comments'), response => {
                    this.$store.commit('hideMessage');
                    this.comments = response;
                }).fail(err => {
                    addAlert(err.responseJSON);
                });
            },

            updateStatus(bool, token, key) {
                $.ajax({
                    type: 'PUT',
                    url: Routing.generate('change_comment_status', {status: '' + bool + '', token: token}),
                    success: response => {
                        this.comments[key].published = bool;
                        addAlert(response);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    }
                });
            },

            deleteComment(token, key) {
                $.ajax({
                    type: 'DELETE',
                    url: Routing.generate('delete_comment', {token: token}),
                    success : response => {
                        this.comments.splice(key, 1);
                        addAlert(response);

                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    }
                });
            },
        },

        created() {
            this.$store.commit('displayWaitingForData');
            this.getComments();
        }
    }
</script>

<style scoped>
    p {
        padding: 5px;
        margin: 0;
    }
</style>