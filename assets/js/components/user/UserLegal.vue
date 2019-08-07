<template>
    <div>
        <h2>{{ t('legal.title')}}</h2>
        <div class="container w40 w95sm" v-if="content">
            <p class="tile white_space" v-html="content"></p>
        </div>
    </div>
</template>

<script>
    import Mixins from '../../mixins';
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";
    import {NAVIGATION_TYPE} from "../../js/variables";

    export default {
        name: 'user-legal',

        data() {
            return {
                content: undefined
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