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
            let $target = $(e.target);
            e.preventDefault();
            clearTimeout(this.timer);

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
                        console.log(response)
                        if (typeof response === 'object') {
                            window.dispatchEvent(new CustomEvent('router-push', {
                                detail: response.slug
                            }));
                        } else {
                            let $searchResultsModal = $('#results-modal');
                            console.log($searchResultsModal.find('#results'))
                            $searchResultsModal.find('#results').empty().append(response);
                            $("#results").find('li').click(e => {
                                window.dispatchEvent(new CustomEvent('hide-results-modal'));
                                window.dispatchEvent(new CustomEvent('router-push', {
                                    detail: $(e.target).data('slug')
                                }));
                            });
                            window.dispatchEvent(new CustomEvent('display-results-modal'));
                        }
                    }
                });
            }, $('.js-app').data('search-timeout'));
        }
    }
});