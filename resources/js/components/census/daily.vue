<template>
  <div class="wrapper">
    <navComponent></navComponent>
    <sidemenuComponent></sidemenuComponent>
    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Census</h1>
            </div>
          </div>
        </div>
      </section>
      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">&nbsp;</h3>
                </div>
                <div class="card-body">
                  <form class="user" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-sm-2">
                        <div class="form-group">
                          <label>From Date</label>
                          <datepicker
                            name="date"
                            required
                            input-class="dpicker"
                            :minimumView="'month'"
                            :maximumView="'month'"
                            v-model="filter.fdate"
                            :bootstrap-styling="true"
                          ></datepicker>
                        </div>
                      </div>

                      <div class="col-sm-2">
                        <div class="form-group">
                          <label>&nbsp;</label> <br />
                          <button
                            type="button"
                            @click="showReport()"
                            class="btn btn-info"
                          >
                            Filter
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>
                  <table class="table table-striped table-bordered table-sm">
                    <thead>
                      <th class="text-center">Day</th>
                      <th class="text-center">Date</th>
                      <th class="text-center">TARGET DAILY CENSUS</th>
                      <th class="text-center">SIMULATED ACTUAL</th>
                      <th class="text-center">NEW TARGET DAILY CENSUS</th>
                      <th class="text-center">REQUIRED DAILY CENSUS</th>
                      <th class="text-center">TARGET BALANCE</th>
                      <th class="text-center">ACTUAL BALance</th>
                    </thead>

                    <tbody>
                      <tr v-for="(e, index) in stns">
                        <td class="text-center">{{ e.day }}</td>
                        <td class="text-center">{{ e.date }}</td>
                        <td class="text-center">{{ e.target }}</td>
                        <td class="text-center">{{ e.actual }}</td>
                        <td class="text-center">{{ e.new_target }}</td>
                        <td class="text-center">{{ e.req_daily_census }}</td>
                        <td class="text-center">{{ e.target_balance }}</td>
                        <td class="text-center">{{ e.actual_balance }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    <footerComponent></footerComponent>
  </div>
</template>

<script type="text/javascript">
import Datepicker from "vuejs-datepicker";
import Select2 from "v-select2-component";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";
export default {
  components: { Datepicker, Select2, Multiselect },
  created() {
    if (!User.loggedIn()) {
      this.$router.push({ name: "/" });
    }

    this.getStns();
  },
  data() {
    return {
      passData: [{}],
      showModal: false,
      myOptions: ["All"],
      hasError: false,
      isHidden: true,
      filter: {
        fdate: null,
      },
      employees: [],
      stns: [],
      searchTerm: "",
      countRecords: 0,
      showing: "",
      getTotalBeds: 0,
      census_results: [],
    };
  },
  computed: {
    filtersearch() {
      return this.employees.filter((e) => {
        return e.patientname.match(this.searchTerm);
      });
    },
    totalLicensedCapacity() {
      return this.stns.reduce((sum, item) => sum + parseFloat(item.licensed), 0);
    },
    totalTargetCapacity() {
      return this.stns.reduce(
        (sum, item) => sum + parseFloat(item.target ? item.target : 0),
        0
      );
    },
    totalManPower() {
      return this.stns.reduce(
        (sum, item) => sum + parseFloat(item.manpower ? item.manpower : 0),
        0
      );
    },
    totalFunctionalCapacity() {
      return this.stns.reduce(
        (sum, item) => sum + parseFloat(item.functional ? item.functional : 0),
        0
      );
    },
    totalOccupiedCapacity() {
      return this.stns.reduce((sum, item) => sum + parseFloat(item.occupied.census), 0);
    },
    totalMgh() {
      return this.stns.reduce((sum, item) => sum + parseFloat(item.mgh.census), 0);
    },
    totalReservation() {
      return this.stns.reduce(
        (sum, item) => sum + parseFloat(item.reservation ? item.reservation : 0),
        0
      );
    },
    totalEr() {
      return this.stns.reduce((sum, item) => sum + parseFloat(item.er ? item.er : 0), 0);
    },
    totalAvailable() {
      return this.stns.reduce(
        (sum, item) => sum + parseFloat(item.available ? item.available : 0),
        0
      );
    },
    totalReseredCapacity() {
      return this.stns.reduce(
        (sum, item) => sum + parseFloat(item.reserved ? item.reserved : 0),
        0
      );
    },
  },
  methods: {
    onSelect(value) {
      if (value == "All") {
        console.log(value);
        this.filter.stns = [];
        this.filter.stns = ["All"];
      } else {
        var index = this.filter.stns.indexOf("All");
        if (index !== -1) {
          this.filter.stns.splice(index, 1);
        }
      }
    },
    updateStatus(e) {
      this.showModal = true;
      this.passData = e;
    },
    getStns() {
      api
        .get("/daily-census")
        .then((response) => {
          this.stns = [];
          response.data.data.forEach((element) => {
            this.stns.push(element);
          });
        })
        .catch((error) => console.log(error));
    },
    showReport() {
      if (this.filter.fdate == null) {
        Toast.fire({
          icon: "error",
          title: "Check fields",
        });
      } else {
        api
          .post("/daily-census", this.filter)
          .then((response) => {
          this.stns = [];
          response.data.data.forEach((element) => {
            this.stns.push(element);
          });
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
      }
    },
    generate() {
      console.log(this.myValue);
    },
    myChangeEvent(val) {
      console.log(val);
    },
    mySelectEvent({ id, text }) {
      console.log({ id, text });
    },
    allEmployee() {
      this.isHidden = false;
      axios
        .get("/api/patientEmployee")
        .then(
          ({ data }) => (
            (this.employees = data[0].data),
            (this.countRecords = data[0].count),
            (this.showing = data[0].showing),
            (this.isHidden = true)
          )
        )
        .catch();
    },
    async check_doctors_detail(id) {
      return await axios.get("/api/check_doctors_detail/" + id).then((response) => {
        setTimeout(function () {
          return response.data;
        }, 3000);
      });
      /*  .then((response) => {  
                return  Promise.resolve(response.data); }) */
    },
    /* async  check_doctors_detail(id) {   
             return await axios.get( '/api/check_doctors_detail/'+id)
            }, */
    formatDate(date) {
      const options = { year: "numeric", month: "long", day: "numeric" };
      return new Date(date).toLocaleDateString("en", options);
    },
    deleteRecord(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.isConfirmed) {
          axios
            .delete("/api/employee/" + id)
            .then(() => {
              this.employees = this.employees.filter((e) => {
                return e.id != id;
              });
            })
            .catch(() => {
              //this.$router.push({name: 'all_employee'})
              this.$router.push("/all_employee").catch(() => {});
            });

          Swal.fire("Deleted!", "Your file has been deleted.", "success");
        }
      });
    },
    filterEmployee() {
      this.employees = [];
      this.countRecords = null;
      this.form.start = 0;
      this.isHidden = false;
      //axios.post('/api/filterEmployee',this.form)
      axios
        .post("/api/patientEmployee", this.form)

        .then((res) => {
          this.employees = res.data[0].data;
          this.countRecords = res.data[0].count;
          console.log(res.data.data);
          this.isHidden = true;
        })
        .catch((error) => (this.errors = error.response.data.errors));
    },
    getPageNo(id) {
      this.form.start = (id - 1) * 10;
      this.isHidden = false;
      //alert(a)
      /* this.employees = []
              this.countRecords = null */
      //axios.post('/api/filterEmployee',this.form)

      axios
        .post("/api/patientEmployee", this.form)
        .then((res) => {
          this.employees = res.data[0].data;
          this.countRecords = res.data[0].count;
          (this.showing = res.data[0].showing), console.log(res.data[0]);
          this.isHidden = true;
        })
        .catch((error) => (this.errors = error.response.data.errors));
    },

    download(ppspat, ddoctor) {
      window.open("/api/print_prescription/" + ppspat + "/" + ddoctor);
    },
  },
  /* mounted () {
          axios.get('/api/check_doctors_detail/'+id)
              .then(response => (this.getdctr = response))
        }, */
  /* created(){
            this.allEmployee();
        } */
};
</script>

<style>
.em_photo {
  height: 40px;
  width: 40px;
}

.to-right {
  float: right;
}

.spin_center {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 300px;
  text-align: center;
  transform: translateX(-50%);
  /*display: none;*/
}

.spin_center2 {
  top: 50%;
  left: 50%;
  width: 300px;
  text-align: center;
  transform: translateX(-50%);
  /*display: none;*/
}

.btn-app {
  height: unset !important;
  padding: 0 1.5em 0 1.5em;
}
</style>

<style>
/* Center the loader */
#loader {
  position: absolute;
  left: 50%;
  top: 50%;
  z-index: 1;
  width: 120px;
  height: 120px;
  margin: -76px 0 0 -76px;
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% {
    -webkit-transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
  }
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Add animation to "page content" */
.animate-bottom {
  position: relative;
  -webkit-animation-name: animatebottom;
  -webkit-animation-duration: 1s;
  animation-name: animatebottom;
  animation-duration: 1s;
}

@-webkit-keyframes animatebottom {
  from {
    bottom: -100px;
    opacity: 0;
  }
  to {
    bottom: 0px;
    opacity: 1;
  }
}

@keyframes animatebottom {
  from {
    bottom: -100px;
    opacity: 0;
  }
  to {
    bottom: 0;
    opacity: 1;
  }
}

#myDiv {
  display: none;
  text-align: center;
}

.hasDetails {
  box-shadow: 14px 0px 0px 0px #00ce6e inset;
}

colgroup col.success {
  background-color: #ffff53;
}

colgroup col.red1 {
  background-color: #fdc7b5;
}

colgroup col.red2 {
  background-color: #fa9a61;
}

colgroup col.blue {
  background-color: #60d5fb;
}

colgroup col.green {
  background-color: #b8fbb5;
}
</style>
