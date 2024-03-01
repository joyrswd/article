<template>
    <div class="row" v-if="items.length">
        <div class="col-md-4 d-flex flex-column justify-content-between p-4 mb-4 position-relative" v-for="item in items">
            <div v-if=item._links.image.href class="backgroundImage">
                <img :src="item._links.image.href" alt="image">
            </div>
            <h4 class="align-self-start"><span>{{ item.title }}</span></h4>
            <p class="align-self-stretch"><span>{{ item.content.substring(0, 150) }}...</span></p>
            <p class="text-end align-self-end"><router-link :to="`${item._links.self.href}`"
                    class="badge rounded-pill btn btn-info" role="button">read more &raquo;</router-link></p>
        </div>
    </div>
    <div class="text-center my-5" v-else>
        <h6>No posts found.</h6>
    </div>
</template>
<script>
export default {
    name: 'List',
    props: ['items']
};
</script>