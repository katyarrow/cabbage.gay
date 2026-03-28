<script setup>
import constants from '#root/constants';
import VButton from './VButton.vue';

</script>

<template>
    <p>
        Enjoying this site?
        <VButton size="xs" :href="constants.URL_DONATION_PAGE" target="_blank">Consider Donating</VButton>
    </p>
</template>