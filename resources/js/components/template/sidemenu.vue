<template>
  <aside id="sidebar" class="main-sidebar sidebar-dark-primary elevation-4">
    <router-link to="/census" class="brand-link">
      <img
        :src="'../../../../public/backend2/rmci.png'"
        alt="AdminLTE Logo"
        class="brand-image img-circle elevation-3"
        style="opacity: 0.8"
      />
      <span class="brand-text font-weight-light">Census</span> <br />
    </router-link>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <user-info></user-info>
        </div>
      </div>
      <nav class="mt-2">
        <ul
          class="nav nav-pills nav-sidebar flex-column"
          data-widget="treeview"
          role="menu"
          data-accordion="false"
        >
          <li class="nav-item">
            <router-link to="/census" class="collapse-item">
              <a
                href="#"
                class="nav-link"
                id="revenuemenu"
                :class="{ active: this.$route.name == 'census' }"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Census</p>
              </a>
            </router-link>
          </li>

          <li
            class="nav-item"
            :class="{
              'menu-open':
                this.$route.name == 'er_list' ||
                this.$route.name == 'er_report'
            }"
          >
            <a
              href="#"
              class="nav-link"
              :class="{
                active:
                  this.$route.name == 'er_list' ||
                  this.$route.name == 'er_report' 
              }"
            >
              <i class="nav-icon fas fa-folder-plus"></i>
              <p>ER Module</p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <router-link to="/er_list">
                  <a
                    href="#"
                    class="nav-link"
                    :class="{
                      active:
                        this.$route.name == 'er_list'
                    }"
                  >
                    <i class="fas fa-user-injured"></i>
                    <p>List</p>
                  </a></router-link
                >
              </li>
              <li class="nav-item">
                <router-link to="/er_report">
                  <a
                    href="#"
                    class="nav-link"
                    :class="{
                      active:
                        this.$route.name == 'er_report' 
                    }"
                  >
                    <i class="fas fa-user-md"></i>
                    Reports
                  </a></router-link
                >
              </li>              
            </ul>
          </li>

          <li class="nav-item">
            <router-link to="/logout" class="nav-link">
              <p>Logout</p>
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </aside>
</template>
<script>
export default {
  created() {},
  data() {
    return {
      img: "../../../../backend2/premier.jpg",
      avatar: "../../../../backend2/dist/img/avatar3.png",
      UserType: User.user_type(),
    };
  },
};
</script>
