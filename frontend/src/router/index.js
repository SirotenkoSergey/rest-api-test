import Vue from 'vue'
import Router from 'vue-router'
import Clients from '@/components/Clients.vue'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Clients',
      component: Clients
    }
  ]
})
