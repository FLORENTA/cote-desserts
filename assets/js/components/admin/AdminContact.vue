<template>
    <transition name="fade">
        <div class="container w40 w95sm" v-if="contacts !== undefined">
            <div class="tile mt5" v-for="(contact, key) in contacts">
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <b>Date: {{ contact.date|formatDate }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Email: </b>{{ contact.email }}</td>
                        </tr>
                        <tr>
                            <td><b>Message: </b><br><br>{{ contact.message }}</td>
                        </tr>
                    </tbody>
                </table>
                <button class="button-delete mauto" v-on:click="deleteContact(contact.token, key)">
                    <i class="fa fa-trash"></i>
                    &nbsp;{{ t('admin.contact.button.delete') }}
                </button>
            </div>
        </div>
    </transition>
</template>
<script>
    import {Routing} from './../../js/routing';
    import {addAlert} from "../../js/alert";

    export default {
        name: 'admin-contact',

        data() {
            return {
                contacts: undefined
            }
        },

        filters: {
            formatDate(date) {
                return new Date(date).toLocaleDateString();
            }
        },

        methods: {
            deleteContact(token, key) {
                $.ajax({
                    type: 'DELETE',
                    url: Routing.generate('delete_contact', {token: token}),
                    success: response => {
                        addAlert(response);
                        this.contacts.splice(key, 1);
                    },
                    error: err => {
                        addAlert(err.responseJSON);
                    }
                });
            }
        },

        created() {
            $.get(Routing.generate('fetch_contacts'), response => {
                this.contacts = response;
            }).fail(err => {
                addAlert(err.responseJSON);
            });
        }
    }
</script>