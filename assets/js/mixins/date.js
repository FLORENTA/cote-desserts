export const date = {
    filters: {
        formatShortDate(date) {
            return new Date(date).toLocaleDateString();
        },

        formatFullDate(date) {
            return 'Le ' + new Date(date).toLocaleString();
        }
    }
};