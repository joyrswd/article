<template>
  <div>
    <button @click="showModal = true" class="btn btn-sm btn-success">Gallery</button>

    <div v-if="showModal" class="modal">
      <span class="close" @click="showModal = false">×</span>
      <ul class="row">
        <li v-for="(item, index) in infoList" :key="index" class="col-md-3">
          <span><img :src="item._links.src.href" @click="loadImage" :data-index="index" alt="image"></span>
        </li>
        <li v-if="nextLink && nextLink.value !== ''" class="next"><button @click="loadNextPage"
            class="btn btn-sm btn-info">Load more</button></li>
      </ul>
      <div v-if="showCarousel" class="carouselContainer">
        <div id="carouselExampleCaptions" class="carousel slide">
          <div class="carousel-inner">
            <a v-for="(item, index) in infoList" :class="{'carousel-item':true, 'active':activeImage === index}" @click="movePage" :href="`${item._embedded.post._links.self.href}`">
                <img :src="item._links.src.href" alt="image" class="d-block">
                <div class="carousel-caption">
                  <h5>{{ item._embedded.post.title }}</h5>
                </div>
              </a>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
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
    const activeImage = ref('');
    const showCarousel = ref(false);

    const getInfoList = async (url) => {
      const response = await axios.post(url);
      infoList.value = infoList.value.concat(response.data?.data ?? []);
      nextLink.value = response.data?._links.next.href ?? '';
    };

    const loadNextPage = async () => {
      await getInfoList(nextLink.value);
    }

    const loadImage = async (e) => {
      activeImage.value = Number(e.target.dataset.index);
      showCarousel.value = true;
    }

    return { showModal, infoList, nextLink, showCarousel, activeImage, getInfoList, loadNextPage, loadImage };
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

.carouselContainer {
  position: fixed;
  z-index: 2;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
}

.carouselContainer>div {
  margin: auto;
  padding: 0 5%;
  width: 90%;
  flex-basis: min-content;
}

.carouselContainer>div img.d-block {
  max-width: 100vw;
}

.carouselContainer>div button {
  width: 5%;
}

.carouselContainer>div .carousel-caption {
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

.modal ul li {
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
  z-index: 10;
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

.modal ul li.next button {
  max-height: 2rem;
}
</style>