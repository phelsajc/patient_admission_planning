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
                  <!-- <form class="user" enctype="multipart/form-data">
                    <div class="row">
                      <div class="col-sm-2">
                        <div class="form-group">
                          <label>From</label>
                          <datepicker
                            name="date"
                            v-model="filter.fdate"
                            required
                            input-class="dpicker"
                            :bootstrap-styling="true"
                          ></datepicker>
                        </div>
                      </div>
                      <div class="col-sm-2">
                        <div class="form-group">
                          <label>To</label>
                          <datepicker
                            name="date"
                            v-model="filter.tdate"
                            required
                            input-class="dpicker"
                            :bootstrap-styling="true"
                          ></datepicker>
                        </div>
                      </div>
                      <div class="col-sm-5">
                        <div class="form-group">
                          <label>Stations</label>
                          <multiselect
                            v-model="filter.stns"
                            :multiple="true"
                            :options="myOptions"
                            @select="onSelect"
                          ></multiselect>
                        </div>
                      </div>
                      <div class="col-sm-3">
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
                  </form> -->

                  <!-- <dl class="row">
                    <dt class="col-sm-2">Total Regular Beds:</dt>
                    <dd class="col-sm-8">{{ getTotalBeds }}</dd>
                  </dl> -->

                  <!-- <table class="table">
                    <thead>
                      <tr>
                        <th>Stations</th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr v-for="e in census_results">
                        <td>
                          <strong>{{ e.station }}</strong>
                        </td>
                        <table class="table">
                          <thead>
                            <tr>
                              <th>Date</th>
                              <th>Bed Capacity</th>
                              <th>Regular Beds</th>
                              <th>Bassinets</th>
                              <th>Occupancy Rate</th>
                              <th>ALOS</th>
                            </tr>
                          </thead>

                          <tbody>
                            <tr v-for="f in e.station_detail">
                              <td>
                                {{ f.date }}
                              </td>
                              <td>
                                {{ f.bedCapacity }}
                              </td>
                              <td>
                                {{ f.occupiedBeds_2 }}
                              </td>
                              <td>
                                {{ f.bassinets }}
                              </td>
                              <td>
                                {{ f.occupanyRate }}
                              </td>
                              <td>
                                {{ f.alos }}
                              </td>
                            </tr>
                            <tr>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td></td>
                              <td>
                                {{ e.total }}
                              </td>
                              <td></td>
                            </tr>
                          </tbody>
                        </table>
                      </tr>
                    </tbody>
                  </table> -->

                  <table class="table table-striped table-bordered table-sm">
                    <!-- <thead>
                      <th class="text-center"></th>
                      <th class="text-center">LICENSED CAPACITY</th>
                      <th class="text-center">TARGET CAPACITY</th>
                      <th class="text-center">MANPOWER HEADCOUNT</th>
                      <th class="text-center">FUNCTIONAL CAPACITY</th>
                      <th class="text-center">OCCUPIED CAPACITY</th>
                      <th class="text-center">MGH</th>
                      <th class="text-center">RESERVATION</th>
                      <th class="text-center">ER</th>
                      <th class="text-center">AVAILABLE</th>
                      <th class="text-center">RESERVED CAPACITY</th>
                    </thead> -->
                    <thead>
                      <th class="text-center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                      <th class="text-center">LICENSED CAPACITY</th>
                      <th class="text-center">TARGET CAPACITY</th>
                      <th class="text-center">MANPOWER HEADCOUNT</th>
                      <th class="text-center">FUNCTIONAL CAPACITY</th>
                      <th class="text-center">OCCUPIED CAPACITY</th>
                      <th class="text-center">MGH</th>
                      <th class="text-center">RESERVATION</th>
                      <th class="text-center">ER</th>
                      <th class="text-center">AVAILABLE</th>
                      <th class="text-center">RESERVED CAPACITY</th>
                      <th class="text-center">RATIO</th>
                      <th class="text-center">ACTION</th>
                    </thead>

                    <tbody>
                      <tr>
                        <td class="text-center" style="width: 6.5%;"><strong>TOTAL</strong></td>
                        <td class="text-center" style="width: 10.5%;">
                          <button type="button" class="btn btn-success btn-sm">
                            {{
                            totalLicensedCapacity
                          }}</button>
                        </td>
                        <td class="text-center" style="width: 9%;"><button type="button" class="btn btn-success btn-sm">{{ totalTargetCapacity }}</button></td>
                        <td class="text-center" style="width: 13%;"><button type="button" class="btn btn-success btn-sm">{{ totalManPower }}</button></td>
                        <td class="text-center" style="width: 11.5%;"><button type="button" class="btn btn-success btn-sm">{{ totalFunctionalCapacity }}</button></td>
                        <td class="text-center" style="width: 10.5%;"><button type="button" class="btn btn-success btn-sm">{{ totalOccupiedCapacity }}</button></td>
                        <td class="text-center"><button type="button" class="btn btn-success btn-sm">{{ totalMgh }}</button></td>
                        <td class="text-center"  style="width: 2%;"><button type="button" class="btn btn-success btn-sm">{{ totalReservation }}</button></td>
                        <td class="text-center"><button type="button" class="btn btn-success btn-sm">{{ totalEr }}</button></td>
                        <td class="text-center"  style="width: 4%;"><button type="button" class="btn btn-success btn-sm">{{ totalAvailable }}</button></td>
                        <td class="text-center"><button type="button" class="btn btn-success btn-sm">{{ totalReseredCapacity }}</button></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <table class="table table-striped table-bordered table-sm">
                    <!-- <thead>
                      <th style="text-align:left" colspan="5">Date</th>
                    </thead> -->
                    <colgroup>
                      <col>
                      <col class="red1">
                      <col class="red2">
                      <col>
                      <col class="blue">
                      <col class="success">OCCUPIED CAPACITY
                      <col class="success">MGH
                      <col class="success">RESERVATION
                      <col class="success">ER
                      <col class="green">
                      <col class="red1">
                      <col>
                      <col>
                    </colgroup>
                    
                    <thead>
                      <th class="text-center">STN</th>
                      <th class="text-center">LICENSED CAPACITY</th>
                      <th class="text-center">TARGET CAPACITY</th>
                      <th class="text-center">MANPOWER HEADCOUNT</th>
                      <th class="text-center">FUNCTIONAL CAPACITY</th>
                      <th class="text-center">OCCUPIED CAPACITY</th>
                      <th class="text-center">MGH</th>
                      <th class="text-center">RESERVATION</th>
                      <th class="text-center">ER</th>
                      <th class="text-center">AVAILABLE</th>
                      <th class="text-center">RESERVED CAPACITY</th>
                      <th class="text-center">RATIO</th>
                      <th class="text-center">ACTION</th>
                    </thead>

                    <tbody>
                      <tr v-for="(e, index) in stns">
                        <td class="text-center">{{ e.station }}</td>
                        <td class="text-center">{{ e.licensed }}</td>
                        <td class="text-center">{{ e.target }}</td>
                        <td class="text-center">{{ e.manpower }}</td>
                        <td class="text-center">{{ e.functional }}</td>
                        <td class="text-center">{{ e.occupied.census }}</td>
                        <td class="text-center">{{ e.mgh.census }}</td>
                        <td class="text-center">{{ e.reservation }}</td>
                        <td class="text-center">{{ e.er }}</td>
                        <td class="text-center">{{ e.available }}</td>
                        <td class="text-center">{{ e.reserved }}</td>
                        <td class="text-center">{{ e.ratio }}</td>
                        <td>
                          <button
                            type="button"
                            class="btn btn-primary"
                            @click="updateStatus(e)"
                          >
                            UPDATE
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <acuityLevel
          v-if="showModal"
          @close="showModal = false"
          v-on:close="getStns"
          :dataArr="passData"
        ></acuityLevel>
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
        stns: [],
        tdate: null,
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
      console.log(1);
      this.stns = [];
      api
        .get("/getStations")
        .then((response) => {
          response.data.data.forEach((element) => {
            this.stns.push(element);
          });
        })
        .catch((error) => console.log(error));
    },
    showReport() {
      console.log(this.filter.stns.length);
      //if(this.filter.stns.length==0&&this.filter.fdate==null&&this.filter.tdate==null){
      if (
        this.filter.stns.length == 0 ||
        this.filter.fdate == null ||
        this.filter.tdate == null
      ) {
        //if(true&&false&&false){

        Toast.fire({
          icon: "error",
          title: "Check fields",
        });
      } else {
        console.log(111);
        api
          .post("/getCensus", this.filter)
          .then((response) => {
            this.census_results = response.data.data;
            this.getTotalBeds = response.data.totalRegularBed;
            Toast.fire({
              icon: "success",
              title: "Saved successfully",
            });
            //this.progressStatus = true;
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
