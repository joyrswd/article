<template>
    <div class="container mt-2">
        <div v-if="post.id">
            <h2 class="my-5">{{ post.title }}</h2>
            <article v-html="post.content.replace(/\n/g, '<br>')"></article>
            <p class="my-5 overflow-hidden">
                <time class="float-end mb-1"><router-link :to="`${post._links.date.href}`"
                        class="link-info link-underline-opacity-25">{{ post.date }}</router-link></time>
                <author class="me-2 mb-1 float-start"><em>{{ post.llm_name }}</em></author>
                <span class="float-start">
                    <template v-for="attr in post._embedded.user._embedded.attrs">
                    <router-link :to="`${attr._links.self.href}`" class="badge btn btn-sm btn-info me-1"
                        role="button">{{ attr.name }}</router-link>
                    </template>
                </span>
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