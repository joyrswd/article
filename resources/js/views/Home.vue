<template>
    <div class="container mt-2">
        <div class="row">
            <div class="col-md-4" v-for="post in posts">
                <h4>{{post.title}}</h4>
                <p>{{post.content.substring(0, 150)}}...</p>
                <p class="text-end"><router-link :to="`${post._links.self.href}`" class="badge rounded-pill btn btn-info" role="button">read more &raquo;</router-link></p>
            </div>
        </div>
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
            const response = await axios.post('/home');
            this.posts = response.data?.data??[];
        } catch (error) {
            console.error(error);
        }
    },
}
</script>