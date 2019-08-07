<template>
    <div>
        <transition name="fade">
            <div class="container w40 w95sm" id="statistic-form-container" v-show="isStatisticFormLoaded"></div>
        </transition>

        <div class="container w40 w95sm">
            <button class="button-delete" v-on:click="deleteStatistics">{{ t('admin.statistics.button.delete') }}</button>
        </div>

        <transition name="fade" class="ml5">
            <div class="container tile w40 w95sm" v-if="total !== 0">
                <table>
                    <thead>
                        <tr>
                            <th>{{ t('statistics.table.head.detail') }}</th>
                            <th>{{ t('statistics.table.head.total') }}</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>{{ total }}</td>
                            <td>100%</td>
                        </tr>
                        <tr v-for="(stat, key) in stats">
                            <td>{{ key }}</td>
                            <td>{{ stat }}</td>
                            <td>{{ ((stat/total)*100).toFixed(0) }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </transition>
    </div>
</template>

<script>
    import Mixins from './../../mixins/index';
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";

    export default {
        name: "admin-statistic",

        data() {
            return {
                stats: [],
                total: 0,
                isStatisticFormLoaded: false
            }
        },

        methods: {
            deleteStatistics() {
                $.ajax({
                    type: 'DELETE',
                    url: Routing.generate('delete_statistics'),
                    success: response => {
                        addAlert(response);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    }
                })
            },

            handleStatisticsFormSubmission(e) {
                let $form = $(e.target)[0];
                let formData = new FormData($form);

                $.ajax({
                    type: 'POST',
                    url: $form.action,
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: statistics => {
                        this.total = statistics['total'];
                        this.stats = this.sortData(statistics['data'])
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    }
                });
            },

            sortData(obj) {
                let sortedObjArray = Object.keys(obj).sort((a, b) => obj[b] - obj[a]);
                let newObj = {};
                sortedObjArray.forEach(key => {newObj[key] = obj[key]});
                obj = {};
                return newObj;
            }
        },

        mixins: [Mixins],

        mounted() {
            $.get(Routing.generate('fetch_statistic_form'), response => {
                $('#statistic-form-container').append(response);
                this.isStatisticFormLoaded = true;
            });

            $(document).on('submit', 'form[name="appbundle_statistic"]', e => {
                e.preventDefault();
                this.handleStatisticsFormSubmission(e);
            });
        }
    }
</script>