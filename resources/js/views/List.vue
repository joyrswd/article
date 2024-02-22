<template>
    <div class="row" v-if="items.length">
        <div class="col-md-4 d-flex flex-column justify-content-between p-4" v-for="item in items">
            <h4 class="align-self-start">{{ item.title }}</h4>
            <p class="align-self-stretch">{{ item.content.substring(0, 150) }}...</p>
            <p class="text-end align-self-end"><router-link :to="`${item._links.self.href}`" class="badge rounded-pill btn btn-info"
                    role="button">read more &raquo;</router-link></p>
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