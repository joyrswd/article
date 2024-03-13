<template>
    <ul class="container">
        <li><input type="date" name="date" id="dateSelector" :max="today" v-model="selectedDate"></li>
        <li><a href="/contact">Contact</a></li>
        <li><a :href="'/rss/' + lang + '.xml'" target="_blank">RSS</a></li>
    </ul>
</template>

<script>
import { ref, computed, watch } from 'vue';

export default {
    setup() {
        const today = computed(() => new Date().toISOString().split('T')[0]);
        const selectedDate = ref('');
        const lang = document.querySelector('html').lang;

        watch(selectedDate, (newValue) => {
            const date = new Date(newValue);
            if (!isNaN(date.getDate())) {
                location.href = '/date/' + newValue;
            }
        });

        return { today, selectedDate, lang }
    }
}
</script>
