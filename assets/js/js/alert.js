import {store} from "./../store";

export let addAlert = message => {
    store.commit('displayServerMessage', message);
};

export let hideMessage = () => {
    store.commit('hideMessage');
};