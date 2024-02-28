<template>
    <div class="container mt-2">
        <h3 class="text-center my-5">{{this.$route.params.id}}</h3>
        <List :items=posts></List>
        <hr>
        <p class="text-center"><router-link to="/" class="btn btn-outline-info">Home</router-link></p>
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
            const response = await axios.post('/date/' + this.$route.params.id);
            this.posts = response.data?.data??[];
            document.title = this.$route.params.id + ' - ' + document.querySelector('h1').textContent;
            document.getElementById('dateSelector').value = this.$route.params.id;
        } catch (error) {
            console.error(error);
        }
    },
}
</script>