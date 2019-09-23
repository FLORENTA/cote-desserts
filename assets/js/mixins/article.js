import {addAlert} from "./../js/alert";
import {Routing} from './../js/routing'
import {spinner} from "./spinner";

export const article = {
    data() {
        return {
            categories: undefined
        }
    },

    mixins: [spinner],

    methods: {
        handleInputCategory(e) {
            let $target = $(e.target);
            // Clear already proposed categories
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
        },

        handleArticleFormSubmission(e) {
            e.preventDefault();
            let $form = $(e.target)[0];
            let $submitButton = $('#appbundle_article_submit');

            this.addButtonLoader($submitButton);

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
                complete: () => {
                    this.removeButtonLoader($submitButton);
                }
            });
        }
    },

    created() {
        $.get(Routing.generate('fetch_categories'), response => {
            this.categories = response;
        });
    }
};