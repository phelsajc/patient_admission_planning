<template>
  <div class="wrapper">
    <navComponent></navComponent>
    <sidemenuComponent></sidemenuComponent>
    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>&nbsp;</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <a href="#">Home</a>
                </li>
                <li class="breadcrumb-item active">ACPN</li>
              </ol>
            </div>
          </div>
        </div>
      </section>
      <section class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">ER Report</h3>
            </div>

            <div class="card-body">
              <form class="user" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>ER Date</label> <br>
                      <label>From</label>
                      <datetime v-if="filter.adm_frm_date=='' && filter.adm_to_date=='' && filter.dsh_frm_date=='' && filter.dsh_to_date==''" format="YYYY-MM-DD h:i:s" v-model='filter.er_frm_date' firstDayOfWeek="1"></datetime>
                      <label>To</label>
                      <datetime v-if="filter.adm_frm_date=='' && filter.adm_to_date=='' && filter.dsh_frm_date=='' && filter.dsh_to_date==''" format="YYYY-MM-DD h:i:s" v-model='filter.er_to_date' firstDayOfWeek="1"></datetime>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>Admission Date</label> <br>
                      <label>From</label>
                      <datetime :disabled="filter.er_frm_date=='' && filter.er_to_date=='' && filter.dsh_frm_date=='' && filter.dsh_to_date==''" format="YYYY-MM-DD h:i:s" v-model='filter.adm_frm_date' firstDayOfWeek="1"></datetime>
                      <label>To</label>
                      <datetime :disabled="filter.er_frm_date=='' && filter.er_to_date=='' && filter.dsh_frm_date=='' && filter.dsh_to_date==''" format="YYYY-MM-DD h:i:s" v-model='filter.adm_to_date' firstDayOfWeek="1"></datetime>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label >Discharge Date</label> <br>
                      <label>From</label>
                      <datetime  v-if="filter.er_frm_date=='' && filter.er_to_date=='' && filter.adm_frm_date=='' && filter.adm_to_date==''" format="YYYY-MM-DD h:i:s" input-class="dtHeight" v-model='filter.dsh_frm_date' firstDayOfWeek="1"></datetime>
                      <label >To</label>
                      <datetime v-if="filter.er_frm_date=='' && filter.er_to_date=='' && filter.adm_frm_date=='' && filter.adm_to_date==''" format="YYYY-MM-DD h:i:s" input-class="dtHeight" v-model='filter.dsh_to_date' firstDayOfWeek="1"></datetime>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>Patient</label><br>
                      <label>&nbsp;</label>
                      <input type="text" class="form-control" id="selectedmed" v-model="filter.patient" />
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group">
                      <label>Acuity Level</label>  <br>
                      <label>&nbsp;</label>                    
                      <select class="form-control" v-model="filter.acquity">
                        <option value="0">All</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>Station</label>    <br> 
                      <label>&nbsp;</label>                 
                      <multiselect
                            v-model="filter.stns"
                            :multiple="true"
                            :options="myOptions"
                            @select="onSelect"
                          ></multiselect>
                    </div>
                  </div>
                  <div class="col-sm-1">
                    <div class="form-group"> 
                      <label>&nbsp;</label><br> <br> 
                      <label>&nbsp;</label>
                      <button type="button" @click="showReport()" class="btn btn-info">
                        Filter
                      </button>
                      <!-- <div class="btn-group" role="group">
                        <button
                          id="btnGroupDrop1"
                          type="button"
                          class="btn btn-primary dropdown-toggle"
                          data-toggle="dropdown"
                          aria-haspopup="true"
                          aria-expanded="false"
                        >
                          Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                          <a class="dropdown-item" href="#" @click="exportPDF()"
                            >PHIC 350</a
                          >
                          <a class="dropdown-item" href="#" @click="exportSharingPDF()"
                            >PHIC 2250</a
                          >
                        </div>
                      </div> -->
                    </div>
                  </div>
                </div>
                <!-- <dl class="row">
                  <dt class="col-sm-2">Total Amount:</dt>
                  <dd class="col-sm-8">
                    {{ totalAmount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                  </dd>
                </dl>
                <dl class="row">
                  <dt class="col-sm-2">Total Session:</dt>
                  <dd class="col-sm-8">
                    {{ total_sessions.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") }}
                  </dd>
                </dl>
                <dl class="row">
                  <dt class="col-sm-2">Doctor:</dt>
                  <dd class="col-sm-8">
                    {{ filter.doctors != "All" ? getDoctorName : "" }}
                  </dd>
                </dl> -->
                <!-- <progressBar :getStatus="showProgress"></progressBar> -->
                <table class="table">
                  <thead>
                    <tr>
                      <th>Patient ID</th>
                      <th>Name</th>
                      <th>ER Date & Time</th>
                      <th>Admission Daste & Time</th>
                      <th>Discharge Date & Time</th>
                      <th>No. of Hours in the ER</th>
                      <th>Admission Status</th>
                      <th>Room No.</th>
                      <th>Station</th>
                      <th>Acuity Level</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="e in results">
                      <td>
                        {{ e.pid }}
                      </td>
                      <td>
                        {{ e.name }}
                      </td>                     
                      <td>
                        {{ e.er }}
                      </td>
                      <td>
                        {{ e.adm }}
                      </td>
                      <td>
                        {{ e.dsh }}
                      </td>
                      <td>
                        {{ e.hrs }}
                      </td>
                      <td>
                        {{ e.status }}
                      </td>
                      <td>
                        {{ e.rm }}
                      </td>
                      <td>
                        {{ e.stn }}
                      </td>
                      <td>
                        {{ e.acuity }}
                      </td>
                      <td>
                     
                      </td>
                    </tr>
                  </tbody>
                </table>
              </form>
            </div>
          </div>
        </div>
        <phicModal
          v-if="showModal"
          @close="showModal = false"
          :sessionid="getsessionid.toString()"
        ></phicModal>
      </section>
    </div>
    <footerComponent></footerComponent>
  </div>
</template>

<script type="text/javascript">
import Datepicker from "vuejs-datepicker";
import datetime from 'vuejs-datetimepicker'
import Multiselect from "vue-multiselect";
import Select2 from "v-select2-component";
import "vue-multiselect/dist/vue-multiselect.min.css";
import moment from "moment";
import api from "../../Helpers/api";

export default {
  created() {
    if (!User.loggedIn()) {
      this.$router.push({ name: "/" });
    }
   /*  this.getBatches();
    this.getDoctors(); */
    this.getStns();
  },
  components: {
    Datepicker,
    datetime,
    Select2, 
    Multiselect
  },
  data() {
    return {
      batches: [],
      pdf: [],
      pdfSharing: [],
      progressStatus: true,
      showModal: false,
      myOptions: ["All"],
      filter: {
        adm_to_date: "",
        adm_frm_date: "",
        dsh_to_date: "",
        dsh_frm_date: "",
        er_frm_date: "",
        er_to_date: "",
        patient: "",
        acquity: "",
      },
      results: [],
      export: [],
      getsessionid: "",
      month: null,
      doctors_list: [],
      getPaidClaims: 0,
      getTotalPaidClaims: 0,
      token: localStorage.getItem("token"),
      getDoctorName: null,
    };
  },
  /* computed: {
    total_sessions() {
      return this.results.reduce((sum, item) => sum + parseFloat(item.sessions), 0);
    },
    getDoctor() {
      return this.doctors_list.find((e) => e.id == this.filter.doctors);
    },
    totalAmount() {
      return this.total_sessions * 350;
    },
    totalAmountPaid() {
      return this.getTotalPaidClaims * 350;
    },
    balance() {
      return this.totalAmount - this.totalAmountPaid;
    },
    unpaid() {
      return this.total_sessions - this.getPaidClaims;
    },
    showProgress() {
      return this.progressStatus;
    },
  }, */
  methods: {
    /* getCompany() {
      api
        .get("getCompanies")
        .then((response) => {
          this.companies = response.data;
        })
        .catch((error) => console.log(error));
    }, */
    showReport() {
      api
        .post("getEeReport", this.filter)
        .then((response) => {
          this.results = response.data.data;
          /* this.getTotalPaidClaims = response.data.getPaidClaims;
          this.pdf = response.data.pdf;
          this.pdfSharing = response.data.sharing;
          this.getPaidClaims = response.data.getPaidClaims;
          this.results = response.data.data;
          this.export = response.data.export;
          this.month = moment(this.filter.date).format("MMMM YYYY"); */
          Toast.fire({
            icon: "success",
            title: "Saved successfully",
          });
          /* this.progressStatus = true; */
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
    getStns() {
      api
        .get("getSDtations")
        .then((response) => {
          response.data.data.forEach((element) => {
            this.myOptions.push(element.station);
          });
        })
        .catch((error) => console.log(error));
    },
    /* getDoctors() {
      api
        .get("getDoctors")
        .then((response) => {
          this.doctors_list = response.data;
        })
        .catch((error) => console.log(error));
    }, */
    getId(id) {
      this.getsessionid = id;
    },
  },
};
</script>

<style>
.pull-right {
  float: right !important;
}

.dpicker {
  background-color: white !important;
}

input[data-v-4bd11526]{
    height:39px !important;
}
</style>
