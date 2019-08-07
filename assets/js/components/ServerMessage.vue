<template>
    <transition name='fade'>
        <div id="server-message" v-show='displayMessage'>
            <slot></slot>
        </div>
    </transition>
</template>

<script>
    export default {
        name: 'server-message',

        props: ['displayMessage'],
    }
</script>