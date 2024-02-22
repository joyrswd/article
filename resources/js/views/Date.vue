<template>
    <div class="container mt-2">
        <h3 class="text-center my-5">{{this.$route.params.id}}</h3>
        <div class="row" v-if="posts.length">
            <div class="col-md-4" v-for="post in posts">
                <h4>{{post.title}}</h4>
                <p>{{post.content.substring(0, 150)}}...</p>
                <p class="text-end"><router-link :to="`${post._links.self.href}`" class="badge rounded-pill btn btn-info" role="button">read more &raquo;</router-link></p>
            </div>
        </div>
        <div class="text-center my-5" v-else>
            <h6>No posts found.</h6>
        </div>
        <hr>
        <p class="text-center"><router-link to="/" class="btn btn-outline-info">Home</router-link></p>
    </div>
</template>
<script>
export default {
    data() {
        return {
            posts: []
        }
    },
    async created() {
        try {
            const response = await axios.post('/date/' + this.$route.params.id);
            this.posts = response.data?.data??[];
        } catch (error) {
            console.error(error);
        }
    },
}
</script>