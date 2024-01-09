
let login = require('./components/auth/login.vue').default;
let logout = require('./components/auth/logout.vue').default
let home = require('./components/home.vue').default

let census = require('./components/census/index.vue').default
let userslist = require('./components/users/index.vue').default
let usersadd = require('./components/users/create.vue').default
let er_list = require('./components/er/index.vue').default
let er_report = require('./components/er/report.vue').default

export const routes = [
    { path: '/', component: login, name: '/' },
    { path: '/logout', component: logout, name: 'forget' },
    { path: '/home', component: home, name: 'home' },
    { path: '/census', component: census, name: 'census' },
    { path: '/userslist', component: userslist, name: 'userslist' },
    { path: '/usersadd/:id', component: usersadd, name: 'usersadd' },
    { path: '/er_list', component: er_list, name: 'er_list' },
    { path: '/er_report', component: er_report, name: 'er_report' },
]
