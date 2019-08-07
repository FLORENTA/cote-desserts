<template>
    <div class="container w40 w95sm" id="contact-form-container" v-show="loaded"></div>
</template>

<script>
    import {Routing} from './../../js/routing';
    import Mixins from '../../mixins';
    import {NAVIGATION_TYPE} from "../../js/variables";

    export default {
        name: 'user-contact',

        data() {
            return {
                loaded: false
            }
        },

        mixins: [Mixins],

        created() {
            this.$store.dispatch('newStatistic', {
                data: this.$route.fullPath,
                type: NAVIGATION_TYPE
            });
        },

        mounted() {
            const el = this;
            this.launchSpinnerAnimation();

            $.get(Routing.generate('fetch_contact_form'), response => {
                $('#contact-form-container').append(response);
                el.cancelSpinnerAnimation();
                this.loaded = true;
            });
        }
    }
</script>