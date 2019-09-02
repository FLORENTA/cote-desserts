import {addAlert} from "../js/alert";

const Mixins = {
    methods: {
        launchSpinnerAnimation() {
            this.$parent.loading = true;
            $(".loading-page-spinner").addClass("fa-spin");
        },

        cancelSpinnerAnimation() {
            this.$parent.loading = false;
            $(".loading-page-spinner").removeClass("fa-spin");
        },

        handleInputCategory(e) {
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
        }
    },

    filters: {
        formatShortDate(date) {
            return new Date(date).toLocaleDateString();
        },

        formatFullDate(date) {
            return 'Le ' + new Date(date).toLocaleString();
        },

        capitalize(val) {
            return val.charAt(0).toUpperCase() + val.slice(1);
        }
    },

    mounted() {
        $(document).on('keyup', '.input-category', this.handleInputCategory);

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

        $(document).on('click', '#remove-pdf', e => {
            e.preventDefault();
            this.$root.$emit('deletePdf', { detail: $(e.currentTarget).data('pdf') });
        });
    },

    beforeDestroy() {
        $(document).off('keyup', '.input-category', this.handleInputCategory);
    }
};

export default Mixins;
