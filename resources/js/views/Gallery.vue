<template>
  <div>
    <button @click="showModal = true" class="btn btn-sm btn-success">Gallery</button>

    <div v-if="showModal" class="modal">
        <span class="close" @click="showModal = false">×</span>
        <ul class="row">
          <li v-for="(item, index) in infoList" :key="index" class="col-md-3">
            <span><img :src="item._links.self.href" alt="image"></span>
          </li>
          <li v-if="nextLink?.value??'' !== ''" class="next"><button @click="loadNextPage" class="btn btn-sm btn-info">Load more</button></li>
        </ul>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const showModal = ref(false);
    const infoList = ref([]);
    const nextLink = ref('');
  
    const getInfoList = async (url) => {
      const response = await axios.post(url);
      infoList.value = infoList.value.concat(response.data?.data ?? []);
      nextLink.value = response.data?._links.next.href ?? '';
    };

    const loadNextPage = async () => {
      await getInfoList(nextLink.value);
    }
  
    return { showModal, infoList, getInfoList, loadNextPage };
  },
  created() {
    this.getInfoList('/gallery');
  }
}
</script>

<style scoped>
.modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  overflow-y: auto;
  background-color: rgba(0, 0, 0, 0.4);
}

.modal ul {
  margin: auto;
  padding: 0;
  max-width: 100vw;
  max-height: 100vh;
  position: relative;
  z-index: 0;
}

.modal ul li{
  list-style-type: none;
  padding: 0;
  margin: 0;
  aspect-ratio: 1;
}

.modal .row span {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal .row span img {
  width: 60%;
  align-items: center;
  transition: 0.3s ease;
  border-radius: 1em;
  cursor: pointer;
  opacity: 0.8;
}

.modal .row span img:hover {
  width: 90%;
  opacity: 1;
}

.close {
  color: #aaaaaa;
  font-size: 28px;
  font-weight: bold;
  position: fixed;
  right: 0;
  top: 0;
  z-index: 1;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

.modal ul li.next {
  width: 100%;
  height: 3rem;
  text-align: center;
}

.modal ul li.next button{
  max-height: 2rem;
}

</style>