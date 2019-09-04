<template>
    <div>
        <h2>{{ t('legal.title')}}</h2>
        <div class="container w40 w95sm" v-if="content">
            <p class="tile white_space" v-html="content"></p>
        </div>
    </div>
</template>

<script>
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";
    import {NAVIGATION_TYPE} from "../../js/variables";
    import {Spinner} from "../../mixins/spinner";

    export default {
        name: 'user-legal',

        data() {
            return {
                content: undefined
            }
        },

        mixins: [Spinner],

        created() {
            this.$store.dispatch('newStatistic', {
                data: this.$route.fullPath,
                type: NAVIGATION_TYPE
            });
        },

        mounted() {
            $.get(Routing.generate('fetch_legal_mentions'), response => {
                this.content = response;
            }).fail(err => {
                addAlert(err.responseJSON);
            }).always(() => {
                this.cancelSpinnerAnimation();
            });
        }
    }
</script>