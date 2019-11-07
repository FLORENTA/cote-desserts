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
                        if ('article' in response) {
                            window.dispatchEvent(new CustomEvent('router-push', {
                                detail: response['article'].slug
                            }));
                        } else if ('titles' in response) {
                            window.dispatchEvent(new CustomEvent('titles', {
                                detail: response['titles']
                            }));
                        }
                    }
                });
            }, $('.js-app').data('search-timeout'));
        }
    }
});