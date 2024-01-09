<template>
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">
          <div class="modal-header">
            <slot name="header" v-if="patientid==0">Add Session</slot>
            <slot name="header" v-else>Edit Session</slot>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="$emit('close')">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <slot name="body">
              <form class="user" @submit.prevent="save" enctype="multipart/form-data">
                <div class="card-body">
                  <div class="form-group">
                    <label>Patient: {{ patient.patient }} </label>
                  </div>                  
                  <div class="form-group">
                    <label>Registry Date: {{ patient.registrydt }} </label>
                  </div>
                  <div class="form-group">
                    <label>Acuity Level</label>
                    <select class="form-control" v-model="form.acuity">
                      <option selected value="0">None</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                    </select>
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
import Datepicker from 'vuejs-datepicker';
//import api from '../../Helpers/api';
export default {
  props: {
    patient: {
            type: Array(),
            default: {}
        },
        doctorId: {
            type: Number,
            default: 0
        },
        scheduleDate: {
            type: String,
            default: null
        },
        patientid: {
            type: Number,
            default: 0
        },
  },
  components: {
    Datepicker,
  },
  watch: {
    sessionid(v) {
      this.form.sessid = v
    },  
    doctorId(v) {
      this.form.doctor = v
    },  
    scheduleDate(v) {
      this.form.schedule = v
    },
    patientid(v) {
      this.form.patientid = v
    },       
  },
  computed:{
  },
  created() {
 /*    this.getDoctors();    
         api.get('patients-detail/'+this.patientid)
        .then(response => {
          console.log("response.data.name")
          console.log(response.data.name)
          return this.patientName = response.data.name
        }).catch(error => {
          if (error.response.data.message == 'Token has expired') {
            this.$router.push({ name: '/' });
            Toast.fire({
              icon: 'error',
              title: 'Token has expired'
            })
          }
        }); */
  },
  data() {
    return {
      form: {
        acuity: null,
        pxdetail: this.patient,
      },
      doctors: [],
      results: [],
      sessionDate: '',
      patientName: '',
      attendingDoctor: '',
    }
  },
  methods: {
    getDoctors() {
      api.get('getDoctors')
        .then(response => {
          this.doctors = response.data
        }).catch(error => {
          if (error.response.data.message == 'Token has expired') {
            this.$router.push({ name: '/' });
            Toast.fire({
              icon: 'error',
              title: 'Token has expired'
            })
          }
        });
    },
    autoComplete() {
      this.results = [];
      if (this.form.searchVal.length >= 3) {
        api.post('patients-find', this.form)
          .then(response => {
            this.results = response.data
          }).catch(error => {
          if (error.response.data.message == 'Token has expired') {
            this.$router.push({ name: '/' });
            Toast.fire({
              icon: 'error',
              title: 'Token has expired'
            })
          }
        });
      } else {
        this.form.patientid = 0
      }
    },
    getPatient(id) {
      this.form.patientid = id.id
      this.form.searchVal = id.name
      this.results = [];
    },
    save() {
      api.post('store-acuity', this.form)
          .then(response => {
            Toast.fire({
              icon: 'success',
              title: 'Saved successfully'
            });
          }).catch(error => {
          if (error.response.data.message == 'Token has expired') {
            this.$router.push({ name: '/' });
            Toast.fire({
              icon: 'error',
              title: 'Token has expired'
            })
          }
        });
    },
    getReturnResponse: function (data) {
      this.form.patientid = data.id.id
      this.attendingDoctor = data.id.doctor
    }
  },
}
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
  