import Vue from 'vue';
import AWN from "awesome-notifications";
import Toolkit from '@bristol-su/frontend-toolkit';
import LoginForm from './components/LoginForm';

Vue.prototype.$notify = new AWN({position: 'top-right'});
Vue.use(Toolkit);

let vue = new Vue({
    el: '#portal-auth-root',

    components: {
        LoginForm
    }
});
