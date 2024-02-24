<template>
    <div class="text-center my-5">
        <h4 class="my-5">Not found.</h4>
        <p class="text-center"><router-link to="/" class="btn btn-outline-info">Home</router-link></p>
    </div>
</template>
<script>
document.title = 'Not found - ' + document.querySelector('h1').textContent;
export default {
    name: 'Notfound',
};
</script>