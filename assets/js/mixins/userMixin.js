export const UserMixin = {
    filters: {
        formatFullDate(date) {
            return 'Le ' + new Date(date).toLocaleString();
        },

        capitalize(val) {
            return val.charAt(0).toUpperCase() + val.slice(1);
        }
    }
};