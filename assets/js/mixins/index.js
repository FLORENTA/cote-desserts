const Mixins = {
    methods: {
        launchSpinnerAnimation() {
            this.$parent.loading = true;
            $(".loading-page-spinner").addClass("fa-spin");
        },

        cancelSpinnerAnimation() {
            this.$parent.loading = false;
            $(".loading-page-spinner").removeClass("fa-spin");
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
};

export default Mixins;
