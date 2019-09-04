export const menu = {
    data() {
        return {
            smallDevice: undefined,
            isMenuDisplayed: false
        }
    },

    methods: {
        showMenu() {
            if (this.smallDevice) {
                let $menu = $('#menu');
                this.isMenuDisplayed
                    ? $menu.removeClass('display_menu')
                    : $menu.addClass('display_menu');

                this.isMenuDisplayed = !this.isMenuDisplayed;
            }
        },

        resetMenu() {
            let $menu = $('#menu');
            if (null !== $menu) {
                $menu.removeClass('display_menu');
                this.menuIsDisplayed = false;
            }
        }
    },

    created() {
        this.smallDevice = window.innerWidth < 1024;
    },
};