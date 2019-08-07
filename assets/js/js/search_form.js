import Vue from 'vue';

new Vue({
    el: '#search-form',
    data() {
        return {
            timer: undefined
        }
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault();
            clearTimeout(this.timer);
            let $target = $(e.target);

            if ($target[0].nodeName === 'INPUT' && $target.val().length === 0) {
                return;
            }

            let $form = $target[0].nodeName === 'INPUT' ? $(e.target.form)[0] : $target[0];
            let formData = new FormData($form);

            this.timer = setTimeout(() => {
                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: response => {
                        if (typeof response === 'object') {
                            window.dispatchEvent(new CustomEvent('router-push', {
                                detail: response.slug
                            }));
                        } else {
                            let $searchResultsModal = $('#search-results-modal');
                            $searchResultsModal.find('#search-results').empty().append(response);
                            $("#search-results").find('li').click(e => {
                                window.dispatchEvent(new CustomEvent('hide-search-results-modal'));
                                window.dispatchEvent(new CustomEvent('router-push', {
                                    detail: $(e.target).data('slug')
                                }));
                            });
                            window.dispatchEvent(new CustomEvent('display-search-results-modal'));
                        }
                    }
                });
            }, $('.js-app').data('search-timeout'));
        }
    }
});