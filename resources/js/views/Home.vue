<template>
    <div class="container mt-2">
        <List :items=posts></List>
    </div>
</template>
<script>
import List from './List.vue';
export default {
    data() {
        return {
            posts: []
        }
    },
    components: {
        List
    },
    async created() {
        try {
            const response = await axios.post('/home');
            this.posts = response.data?.data ?? [];
            document.title = document.querySelector('h1').textContent;
        } catch (error) {
            console.error(error);
        }
    },
}
</script>