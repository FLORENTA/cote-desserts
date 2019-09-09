<template>
    <div class="container w40 w95sm text-centered">
        <p>{{ t('not_found.title') }}</p>
        <i class="fa fa-frown-o fa-4x"></i>
    </div>
</template>

<script>
    import {NAVIGATION_TYPE} from "../js/variables";
    import {spinner} from "../mixins/spinner";

    export default {
        name: 'PageNotFound',

        mixins: [spinner],

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