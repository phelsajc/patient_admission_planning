<template>
  <div class="hold-transition login-page">
    <div class="login-box">
      <div class="card card-outline card-primary">
        <div class="card-header text-center">
        <h2>Patient Admission Planning</h2>
          <!-- <a href="javascript:void(0);" class="h1"><b>Welcome User</b></a> -->
        </div>
        <div class="card-body">
          <p class="login-box-msg">Sign in to start your session</p>
          <form class="user" @submit.prevent="login">
            <div class="input-group mb-3">
              <input
                type="text"
                class="form-control"
                id="exampleInputEmail"
                aria-describedby="emailHelp"
                placeholder="Enter ID Number"
                v-model="form.username"
              />
              <small class="text-danger" v-if="errors.username">{{
                errors.username[0]
              }}</small>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input
                type="password"
                class="form-control"
                id="exampleInputPassword"
                placeholder="Enter Windows Password"
                v-model="form.password"
              />
              <small class="text-danger" v-if="errors.password">{{
                errors.password[0]
              }}</small>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>
            <hr />
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import "../../../../public/backend2/dist/css/adminlte.min.css";
/* {{ asset('backend2/dist/css/adminlte.min.css') }} */
</style>

<script type="text/javascript">
import api from '../../Helpers/api';
export default {
  created() {
    if (!User.loggedIn()) {
      this.$router.push({ name: "/" });
    }
  },

  data() {
    return {
      form: {
        username: null,
        password: null,
      },
      details:{
      data: {
        attributes:{
          code: "123456",
          username: "rdev",
          password: "8e1d74ba536a799fb7e32ae73ae78ea9"
        },
		    type: "login-requests"
      },
      },
      errors: {},
    };
  },
  methods: {
    async login() {
      await axios
        .post("/api/auth/login", this.form)
        .then((res) => {
          User.responseAfterLogin(res);
                    location = '/census'
          Toast.fire({
            icon: "success",
            title: "Signed in successfully!",
          });
       //   this.$router.push({ name: "census" });
        })
        .catch((error) => {
          //if (error.response.data.message == "Token has expired") {
            //this.$router.push({ name: "/" });
            console.log(error)
            Toast.fire({
              icon: "error",
              title: error.response.data.message,
            });
          //}
        });
        /* .catch((error) => (this.errors = error.response.data.errors))
        .catch(
          Toast.fire({
            icon: "warning",
            title: error.response.data.message 
          }),

          console.log(this.errors)
        ); */
    },
  },
};
</script>

<style></style>
