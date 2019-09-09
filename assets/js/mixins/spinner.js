export const spinner = {
    methods: {
        launchSpinnerAnimation() {
            this.$parent.loading = true;
            $(".loading-page-spinner").addClass("fa-spin");
        },

        cancelSpinnerAnimation() {
            this.$parent.loading = false;
            $(".loading-page-spinner").removeClass("fa-spin");
        },

        addButtonLoader($button) {
            if (!$button.hasClass('fa-spinner')) {
                $button.append(
                    $("<span>&nbsp;<i class='fa fa-spinner fa-spin'></i><span>")
                );
            }
        },

        removeButtonLoader($button) {
            $button.find('span').remove();
        }
    }
};