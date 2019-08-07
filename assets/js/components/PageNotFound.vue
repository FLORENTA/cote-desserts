<template>
    <div class="container w40 w95sm text-centered">
        <p>{{ t('not_found.title') }}</p>
        <i class="fa fa-frown-o fa-4x"></i>
    </div>
</template>

<script>
    import Mixin from '../mixins';
    import {NAVIGATION_TYPE} from "../js/variables";

    export default {
        name: 'PageNotFound',

        mixins: [Mixin],

        created() {
            this.$store.dispatch('newStatistic', {
                data: 'pathError',
                type: NAVIGATION_TYPE
            });
        },

        mounted() {
            this.cancelSpinnerAnimation();
        }
    }
</script>