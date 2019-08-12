<template>
  <div class="row no-gutters">
    <div class="main-wrapper col-md-9">
      <div class="wrapper pt-5 mt-4">
        <div class="header text-center">FACEMASH</div>
        <div
          class="sub-header text-center"
        >Were we let in for our looks? No. Will we be judged on them? Yes.</div>
      </div>
      <div class="body-wrapper">
        <div class="row mt-3">
          <div class="col-md-1"></div>
          <div class="col-md-5 text-center" v-if="participant[0]">
            <div class="img-container">
              <img :src="participant[0].url" style="border: 2px solid #000" class="image" />
              <div class="overlay">
                <div class="text">
                  <a href @click.prevent="chooseSomeone(1)">
                    <i class="fas fa-heart"></i>
                  </a>
                  <br />
                  {{ participant[0].name }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-5 text-center" v-if="participant[1]">
            <div class="img-container">
              <img :src="participant[1].url" style="border: 2px solid #000" class="image" />
              <div class="overlay">
                <div class="text">
                  <a href @click.prevent="chooseSomeone(2)">
                    <i class="fas fa-heart"></i>
                  </a>
                  <br />
                  {{ participant[1].name }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mean-header text-center">Who is Hotter? Give her a Heart</div>
      </div>
    </div>
    <div class="side col-md-3">
      <div class="mean-header p-3 text-center">HOT, HOTTER, HOTTEST</div>
      <div class="alert-container mt-1">
        <div
          class="alert alert-danger ml-2 mr-2"
          role="alert"
          v-for="(item, index) in all_participants"
          :key="index"
        >
          <div>
            <img :src="item.url" height="40px" width="40px;" style="border-radius: 50%;" />
            <span class="ml-2">
              <b>{{ item.name }}</b>
            </span>
            <span class="float-right pt-2">~ {{ item.score }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      participant: [],
      all_participants: []
    };
  },
  methods: {
    chooseSomeone(id) {
      axios
        .post("http://localhost:8000/api/participant/submit", {
          first: this.participant[0].id,
          second: this.participant[1].id,
          selected: id
        })
        .then(response => {
          this.refresh_list();
          this.refresh_participant();
        });
    },
    refresh_list(){
      axios.get("http://localhost:8000/api/participants/all").then(response => {
        response.data.data = response.data.data.map(participant => {
          participant.url = "http://localhost:8000/60/" + participant.url;
          return participant;
        });
        this.all_participants = response.data.data;
      });
    },
    refresh_participant(){
      axios.get("http://localhost:8000/api/participant").then(response => {
        response.data.data = response.data.data.map(participant => {
          participant.url = "http://localhost:8000/image/" + participant.url;
          return participant;
        });
        this.participant = response.data.data;
      });
    }
  },
  created() {
    this.refresh_list();
    this.refresh_participant();
  }
};
</script>
