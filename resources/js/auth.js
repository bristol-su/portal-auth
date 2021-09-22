import Vue from 'vue';
import Toolkit from '@bristol-su/frontend-toolkit';
import LoginForm from './components/LoginForm';
import RegisterForm from './components/RegisterForm';
import ForgotPasswordForm from './components/ForgotPasswordForm';

Vue.use(Toolkit);

let vue = new Vue({
    el: '#portal-auth-root',

    components: {
        LoginForm,
        RegisterForm,
        ForgotPasswordForm
    }
});
