<template>
    <div>
        <div class='container w40 w95sm' v-if="article">
            <h2>{{ article.title }}</h2>
            <figure class='tile' v-for="(image, index) in article.images">
                <img v-bind:src="'./images/' + image.src" v-bind:alt='image.title' />
                <a v-bind:style='pdfStyle' v-if="null !== article.pdf && index === 0" v-bind:href="'./images/' + article.pdf"><i class="fa fa-file"></i> {{ t('article.pdf.download') }}</a>
                <figcaption class="white_space" v-html="image.content"></figcaption>
            </figure>

            <p v-if="loaded && article.categories !== undefined && article.categories.length > 0"><b>{{ t('article.categories') }}</b></p>

            <div class="category" v-if="loaded">
                <router-link
                    v-if="article.categories.length > 0"
                    v-for='(category, key) in article.categories'
                    v-bind:key="key"
                    v-bind:to="{name: 'category', params: {category: category.category} }">
                    <button class="button-default">
                        {{ category.category|capitalize }} <i class="fa fa-chevron-right"></i>
                    </button>
                </router-link>
            </div>

            <div class="tile comments-container" v-if='loaded && commentToDisplay' v-on:click="displayComment = !displayComment">
                <i class="fa fa-comments" v-on:click='!displayComment'></i> {{ t('article.comments') }} ({{ article.comments.length }})
            </div>

            <div class="tile-comment" v-if="displayComment">
                <p v-if="displayComment && !commentToDisplay">{{ t('article.no_comment') }}</p>
                <div class="comment" v-if="displayComment && comment.published && article.comments.length > 0" v-for="comment in article.comments">
                    <p>{{ comment.username }}, {{ comment.date|formatFullDate }}</p>
                    <p class="white_space">
                        <i class="fa fa-quote-left fa-xs"></i>&nbsp;&nbsp;{{ comment.comment }}&nbsp;&nbsp;<i class="fa fa-quote-right fa-xs"></i>
                    </p>
                </div>
            </div>
        </div>

        <div id="comment_form_container" class="container w40 w95sm" v-show="showCommentModal"></div>

        <div v-if="loaded && article && !showCommentModal" class='container'>
            <button v-on:click="showForm" class="button-default mauto">
                <i class="fa fa-comment"></i> {{ t('article.button.comment') }}
            </button>
        </div>
    </div>
</template>

<script>
    import Mixins from '../../mixins';
    import {Routing} from './../../js/routing';
    import {addAlert, hideMessage} from "../../js/alert";
    import {NAVIGATION_TYPE} from "../../js/variables";

    export default {
        name: 'user-article',

        data() {
            return {
                article: undefined,
                showCommentModal: false,
                displayComment: false,
                commentToDisplay: false,
                loaded: false,
            }
        },

        mixins: [Mixins],

        computed: {
            pdfStyle() {
                return {
                    display: 'block',
                    margin: '20px 0',
                    padding: '10px 0',
                    borderBottom: '1px solid lightgrey'
                }
            },
            articleId() {
                return this.article.id;
            }
        },

        methods: {
            /* Function to open or close the modal */
            /* Also called in the mixin when the user submits their message */
            showForm() {
                this.showCommentModal = true;
            },

            hideForm() {
                this.showCommentModal = false;
            },

            handleCommentFormSubmission(e) {
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
                        this.showCommentModal = false;
                        this.removeButtonLoader($submitButton);
                    }
                });
            },

            hideCommentModal() {
                this.showCommentModal = false;
            },
        },

        created() {
            this.$store.dispatch('newStatistic', {
                data: this.$route.fullPath,
                type: NAVIGATION_TYPE
            });

            $.get(Routing.generate('article_fetch', { slug: this.$route.params.slug} ), response => {
                this.article = response;

                if (this.article.comments.length !== 0) {
                    this.article.comments.map(comment => {
                        if (comment.published) this.commentToDisplay = true;
                    });
                }

                hideMessage();

                $.get(Routing.generate('fetch_comment_form', { id : this.article.id }), response => {
                    $('#comment_form_container').append(response);
                });
            }).fail(err => {
                addAlert(err.responseJSON);
            }).always(() => {
                this.loaded = true;
                this.cancelSpinnerAnimation();
            });
        },

        mounted() {
            this.launchSpinnerAnimation();

            $(document).on('click', '#close-comment-modal-button', this.hideCommentModal);
            $(document).on('submit', 'form[name="appbundle_comment"]', this.handleCommentFormSubmission);
        },

        beforeDestroy() {
            $(document).off('submit', 'form[name="appbundle_comment"]', this.handleCommentFormSubmission);
            $(document).off('click', '#close-comment-modal-button', this.hideCommentModal);
        }
    }
</script>

<style lang="scss" scoped>
    img {
        width: 100%;
    }
</style>