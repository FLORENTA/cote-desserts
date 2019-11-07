import {addAlert} from "./../js/alert";
import {Routing} from './../js/routing'
import {spinner} from "./spinner";
import {path} from "../js/path";

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
                    if (typeof response === 'object') {
                        if ('alert' in response) {
                            addAlert(response['alert']);
                        }

                        if ('delete_pdf_url' in response) {
                            $('#appbundle_article_pdf_file').parents('.form_row').after($('<p>' + Translator.trans('article.pdf.delete') + '</p>' +
                                '<button class="button-delete" id="remove-pdf" data-delete-pdf-url="' + response['delete_pdf_url'] + '">' +
                                '<i class="fa fa-trash"></i> ' + Translator.trans('admin.article.form.button.remove') + '</button>')
                            );
                        }
                    } else {
                        addAlert(response);
                    }

                    if (this.$router.currentRoute.name === 'adminCreateArticle') {
                        this.$router.push(path.admin);
                    }
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
        $.get(Routing.generate('get_categories'), response => {
            this.categories = response;
        });
    }
};