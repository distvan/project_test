import Vue from 'vue'
import VueResource from 'vue-resource'
import Container from './Container.vue'

Vue.use(VueResource)

export const eventBus = new Vue({})

Object.defineProperties(Vue.prototype, {
    $bus:{
        get: function(){
            return eventBus
        }
    }
})

new Vue({
    el: '#container',
    render: h => h(Container)
})