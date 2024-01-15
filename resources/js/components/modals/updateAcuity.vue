<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <slot name="header">Update Data</slot>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="$emit('close')"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <slot name="body">
              <form class="user" @submit.prevent="save" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="form-group">
                    <label>Station: {{ data.station }} </label>
                  </div>   
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-1 col-form-label"
                      >Reservation</label
                    >
                    <div class="col-sm-2">
                      <input
                        v-model="form.reservation"
                        type="text"
                        class="form-control"
                        placeholder="Reservation"
                      />
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="inputEmail3" class="col-sm-1 col-form-label">ER</label>
                    <div class="col-sm-2">
                      <input
                        v-model="form.er"
                        type="text"
                        class="form-control"
                        placeholder="ER"
                      />
                    </div>
                  </div>
                </div>
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
<script>
import Datepicker from "vuejs-datepicker";
export default {
  props: {
    dataArr: {
      type: Array(),
      default: {},
    },
  },
  components: {
    Datepicker,
  },
  watch: {
    patientid(v){
    }
  },
  computed: {},
  created() {
    
  },
  data() {
    return {
      form: {
        reservation: null,
        er: null,
        id: this.dataArr.id,
        stn: this.dataArr.station,
        occupied: this.dataArr.occupied.census,
        mgh: this.dataArr.mgh.census,
      },
      data: this.dataArr,
    };
  },
  methods: {
    save() {
      api
        .post("update-stn-census", this.form)
        .then((response) => {
          Toast.fire({
            icon: "success",
            title: "Saved successfully",
          });
        })
        .catch((error) => {
          if (error.response.data.message == "Token has expired") {
            this.$router.push({ name: "/" });
            Toast.fire({
              icon: "error",
              title: "Token has expired",
            });
          }
        });
    },
  },
};
</script>
<style scoped>
.container-iframe {
  position: relative;
  overflow: hidden;
  width: 100%;
  padding-top: 56.25%;
}

/* Then style the iframe to fit in the container div with full height and width */
.responsive-iframe {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
}

* {
  box-sizing: border-box;
}

.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  transition: opacity 0.3s ease;
  overflow-x: auto;
}

.modal-container {
  width: 75%;
  height: 100%;
  margin: 149px 309px;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
}

.modal-body {
  margin: 20px 0;
}

/*
           * The following styles are auto-applied to elements with
           * transition="modal" when their visibility is toggled
           * by Vue.js.
           *
           * You can easily play with the modal transition by editing
           * these styles.
           */
.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
}
</style>
