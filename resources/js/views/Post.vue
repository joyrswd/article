<template>
    <div class="container mt-2">
        <div v-if="post.id">
            <h2 class="my-5">{{ post.title }}</h2>
            <article v-html="post.content.replace(/\n/g, '<br>')"></article>
            <p class="my-5">
                <author class="float-start me-2"><em>{{ post.llm_name }}</em></author>
                <span v-for="attr in post._embedded.user._embedded.attrs">
                    <router-link :to="`${attr._links.self.href}`" class="badge btn btn-sm btn-info mx-1"
                        role="button">{{ attr.name }}</router-link>
                </span>
                <time class="float-end"><router-link :to="`${post._links.date.href}`"
                        class="link-info link-underline-opacity-25">{{ post.date }}</router-link></time>
            </p>
        </div>
        <div class="text-center my-5" v-else>
            <h6>No posts found.</h6>
        </div>
        <p class="text-center"><router-link to="/" class="btn btn-outline-info">Home</router-link></p>
    </div>
</template>
<script>
import router from '../router';

export default {
    data() {
        return {
            post:{}
        };
    },
    async created() {
        try {
            const response = await axios.post('/post/' + this.$route.params.id);
            this.post = response.data.data ?? {};
            document.title = document.querySelector('h1').textContent;
            if (this.post?.title) {
                document.title = this.post.title + ' - ' + document.title;
            }
        } catch (error) {
            console.error(error);
        }
    },
    components: { router }
}
</script>