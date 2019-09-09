export const date = {
    filters: {
        formatShortDate(date) {
            return new Date(date).toLocaleDateString();
        }
    }
};