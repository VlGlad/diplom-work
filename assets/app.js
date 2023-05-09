import Vue from 'vue';
import VueRouter from 'vue-router';
import store from "./store/store";

import App from "./components/App.vue";
import Blank from "./components/Right/Blank";
import Right from "./components/Right/Right";
import Feed from "./components/Feed/Feed";
import Add from "./components/Actions/Add";
import Search from "./components/Actions/Search";
import Chat from "./components/Actions/Chat"

Vue.use(VueRouter)

const routes = [
    {
        name: 'blank',
        path: '/',
        component: Blank
    },
    {
        name: 'conversation',
        path: '/conversation/:id',
        component: Right
    },
    {
        name: 'chat',
        path: '/app/chat',
        component: Chat
    },
    {
        name: 'feed',
        path: '/app/feed',
        component: Feed
    },
    {
        name: 'add',
        path: '/app/add',
        component: Add
    },
    {
        name: 'search',
        path: '/app/search',
        component: Search
    }
];

const router = new VueRouter({
    mode: "abstract",
    routes
})

store.commit("SET_USERNAME", document.querySelector('#app').dataset.username);

const curConvId = Vue.observable({ curConvId: -1 })

Object.defineProperty(Vue.prototype, '$curConvId', {
  get () {
    return curConvId.curConvId
  },
  set (value) {
    curConvId.curConvId = value
  }
})

new Vue({
    store,
    router,
    render: h => h(App)
}).$mount('#app')

router.replace('/app/feed')