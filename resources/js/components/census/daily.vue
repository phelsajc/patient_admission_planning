<template>
  <div class="wrapper">
      <Loading :active="isLoading"></Loading>
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
                    <colgroup>
                      <col />
                      <col />
                      <col />
                      <col class="success" />
                      SIMULATED ACTUAL
                      <col />
                      <col />
                      <col />
                      <col />
                      <col />
                    </colgroup>

                    <thead>
                      <th class="text-center">Day</th>
                      <th class="text-center">Date</th>
                      <th class="text-center">TARGET DAILY CENSUS</th>
                      <th class="text-center">SIMULATED ACTUAL</th>
                      <th class="text-center">NEW TARGET DAILY CENSUS</th>
                      <th class="text-center">REQUIRED DAILY CENSUS</th>
                      <th class="text-center">TARGET BALANCE</th>
                      <th class="text-center">ACTUAL BALANCE</th>
                      <th class="text-center">VAR</th>
                    </thead>

                    <tbody>
                      <tr
                        v-for="(e, index) in stns"
                        :class="
                          e.day == 'Sunday' || e.day == 'Saturday' ? 'bg_color' : ''
                        "
                      >
                        <td class="text-center">{{ e.day }}</td>
                        <td class="text-center">{{ e.date }}</td>
                        <td class="text-center">{{ e.target }}</td>
                        <td
                          :class="
                            e.day == 'Sunday' || e.day == 'Saturday' ? 'bg_colorY' : ''
                          "
                          class="text-center"
                        >
                          {{ e.actual }}
                        </td>
                        <td class="text-center">{{ e.new_target }}</td>
                        <td class="text-center">{{ e.req_daily_census }}</td>
                        <td class="text-center">{{ e.target_balance }}</td>
                        <td class="text-center">{{ e.actual_balance }}</td>
                        <td class="text-center">{{ e.var }}</td>
                      </tr>
                    </tbody>
                  </table>

                  <div id="chart">
                    <apexchart
                      type="line"
                      height="350"
                      :options="chartOptions"
                      :series="series"
                    ></apexchart>
                  </div>
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
import VueApexCharts from "vue-apexcharts";
import Loading from "vue-loading-overlay";

export default {
  components: {
    Datepicker,
    Select2,
    Multiselect,
    apexchart: VueApexCharts,
    Loading,
  },
  created() {
    if (!User.loggedIn()) {
      this.$router.push({ name: "/" });
    }
  },
  data() {
    return {
      isLoading: true,
      fullPage: true,
      color: "#ffff53",
      loading: true,
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
      getNewTargetArray: [],
      getsimulatedArray: [],
      gettargetDailyArray: [],
      dateArray: [],
      series: [
        /* {
          name: "Target Daily Census",
          type: "column",
          data: [10, 19, 27, 26, 34, 35, 40, 38],//this.getNewTargetArray,
        },
        {
          name: "Simulated Actual",
          type: "column",
          data: [10, 19, 27, 26, 34, 35, 40, 38],//this.getsimulatedArray,
        },
        {
          name: "New Target Daily",
          type: "line",
          data: [10, 19, 27, 26, 34, 35, 40, 38],//this.gettargetDailyArray,
        }, */
      ],
      chartOptions: {
        chart: {
          height: 350,
        },
        colors: ["#050cf3", "#f49122", "#fc1425", "#00bb00"],
        stroke: {
          width: [0, 4, 2],
        },
        title: {
          text: "Daily Census",
        },
        /* dataLabels: {
          enabled: true,
          enabledOnSeries: [0,1,2],
        }, */
        labels: this.dateArray,
        yaxis: {
        max: 250,
        min: 100
    }
        /* xaxis: {
          type: "dat
          etime",
        }, */
       // yaxis: [
          /* {
            title: {
              text: "Website Blog",
            },
          }, */
          /*  {
            opposite: true,
            title: {
              text: "Social Media",
            },
          }, */
        //],
      },
    };
  },
  methods: {
    async getStns() {
      await api
        .get("/daily-census")
        .then((response) => {
          this.series = [];
          this.stns = [];
          response.data.data.forEach((element) => {
            this.stns.push(element);
          });
          this.series = response.data.series;
          this.chartOptions = {
            xaxis: {
              categories: response.data.daysArr,
            },
          };
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
            this.series = [];
            response.data.data.forEach((element) => {
              this.stns.push(element);
            });
            this.series = response.data.series;
            this.chartOptions = {
              xaxis: {
                categories: response.data.daysArr,
              },
            };
            Toast.fire({
              icon: "success",
              title: "Generated successfully",
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
  },
  mounted() {
    this.getStns();
  },
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

.bg_color {
  background-color: #ff5151 !important;
  color: white;
}

.bg_colorY {
  background-color: #ffff53 !important;
  color: black;
}
</style>
