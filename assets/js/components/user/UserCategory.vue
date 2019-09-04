<template>
    <div>
        <transition name="fade">
            <div class="container w40 w95sm" id="category-form-container" v-show="isCategoryFormLoaded"></div>
        </transition>
        <p class="text-centered" v-if="undefined !== displayedCategory">{{ t('category.article_for_categories') }} <b>{{ displayedCategory }}</b></p>
        <p class="text-centered" v-if="articles.length > 0">{{ articles.length }} {{ t('category.results') }}</p>
        <transition name="fade">
            <div class="container_flex w95sm" v-show="isArticlesLoaded">
                <div v-for='article in articles' class="tile">
                    <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                        <div class='image' :style="{
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                                height: '225px',
                                backgroundImage: 'url('+'./images/' + article.image_src +')'
                             }" v-bind:alt="article.title">
                        </div>
                    </router-link>
                    <h2>{{ article.title }}</h2>
                    <router-link v-bind:to="{name: 'article', params: {slug: article.slug} }">
                        <button class="button-default m10">{{ t('article.access_button') }}</button>
                    </router-link>
                </div>
            </div>
        </transition>
    </div>
</template>
<script>
    import {Routing} from '../../js/routing';
    import 'selectize';
    import {addAlert} from "../../js/alert";
    import {NAVIGATION_TYPE} from "../../js/variables";
    import {Spinner} from "../../mixins/spinner";

    export default {
        name: 'user-category',

        data() {
            return {
                articles: [],
                isCategoryFormLoaded: false,
                displayedCategory: undefined,
                categories: undefined,
                isArticlesLoaded: false
            }
        },

        mixins: [Spinner],

        methods: {
            updateCategories(newVal) {
                let categories = '';
                newVal.map(category => {
                    categories += (category + ', ');
                });
                categories = categories.slice(0, -2);
                this.displayedCategory = categories;
            },

            handleCategoryFormSubmission(e) {
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
            }
        },

        created() {
            this.launchSpinnerAnimation();

            this.$store.dispatch('newStatistic', {
                data: this.$route.fullPath,
                type: NAVIGATION_TYPE
            });
        },

        mounted() {
            /* If coming from an article */
            if (this.$route.name === "category") {
                $.ajax({
                    type: 'GET',
                    url: Routing.generate('fetch_article_for_category', { category: this.$route.params.category }),
                    success: response => {
                        this.articles = response;
                        this.isArticlesLoaded = true;
                        this.displayedCategory = this.$route.params.category;
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    },
                    complete: () => {
                        this.cancelSpinnerAnimation();
                    }
                });
            }

            $.get(Routing.generate('fetch_category_form'), response => {
                $('#category-form-container').append(response);
                $('#appbundle_category_category').selectize({
                    plugins: ['remove_button'],
                    sortField: [{field: 'text'}],
                    // Sort alphabetically each time a new category is added to the form
                    onItemAdd: function (value, $item) {
                        let $parent = $item.parent();
                        let $children = $parent.children();
                        let alphabeticallyOrderedDivs = $children.sort((a, b) => {
                            if ($(a).text() !== "" && $(b).text() !== "") {
                                return $(a).text() > $(b).text();
                            }
                        });

                        $parent.html(alphabeticallyOrderedDivs);
                    }
                });
                this.isCategoryFormLoaded = true;
            }).fail(err => {
                addAlert(err.responseJSON);
            }).always(() => {
                this.cancelSpinnerAnimation();
            });

            this.$root.$on('fetch_article_by_category_result', e => {
                let response = e.detail;
                if ('articles' in response) {
                    this.articles = response.articles;
                    this.isArticlesLoaded = true;
                }

                if ('categories' in response) {
                    this.updateCategories(response.categories);
                }
            });

            $(document).on('submit', 'form[name="appbundle_category"]', this.handleCategoryFormSubmission);
        },

        beforeDestroy() {
            $(document).off('submit', 'form[name="appbundle_category"]', this.handleCategoryFormSubmission);
        }
    }
</script>

<style scoped>
    @media all and (min-width: 1024px) {
        .tile {
            width: 30%!important;
        }
    }

    @media all and (min-width: 768px) and (max-width: 1023px) {
        .tile {
            width: 48%!important;
        }
    }

    @media all and (max-width: 767px) {
        .tile {
            width: 95%!important;
        }
    }
</style>