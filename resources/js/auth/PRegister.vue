<template>
    <v-row
        justify="center"
        align="center"
        class="fill-height">
        <v-col
            cols="12"
            sm="8"
            md="4">
            <validation-observer ref="observer" v-slot="{ invalid, handleSubmit }">
                <v-form method="POST" :action="route" ref="form">
                    <v-card>
                        <v-card-title class="justify-center">
                            <span class="primary--text">Register</span>
                        </v-card-title>
                        <v-card-text>
                            <input type="hidden" :value="csrf" name="_token" id="_token"/>

                            <validation-provider
                                v-slot="{ errors }"
                                name="identifier"
                                rules="required">

                                <v-text-field
                                    :label="getSetting(settingKeys.authentication.identifier.label)"
                                    id="identifier"
                                    name="identifier"
                                    v-model="credentials.identifier"
                                    prepend-icon="mdi-account"
                                    :error-messages="errors"
                                    type="text"
                                    :autofocus="!hasServerErrors('identifier')"
                                ></v-text-field>
                            </validation-provider>


                            <validation-provider
                                v-slot="{ errors }"
                                name="password"
                                rules="required|min:6">

                                <v-text-field
                                    id="password"
                                    label="Password"
                                    name="password"
                                    v-model="credentials.password"
                                    prepend-icon="mdi-lock"
                                    :error-messages="errors"
                                    type="password"
                                ></v-text-field>
                            </validation-provider>

                            <validation-provider
                                v-slot="{ errors }"
                                name="password_confirmation"
                                rules="required|password_confirmed:@password">

                                <v-text-field
                                    id="password_confirmation"
                                    label="Confirm Password"
                                    name="password_confirmation"
                                    v-model="credentials.password_confirmation"
                                    prepend-icon="mdi-lock"
                                    :error-messages="errors"
                                    type="password"
                                ></v-text-field>
                            </validation-provider>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn color="primary" block type="submit" aria-label="Register" :disabled="invalid"
                                   :loading="loading">
                                <v-icon>mdi-arrow-right</v-icon>
                            </v-btn>
                        </v-card-actions>
                        <v-card-text>
                            <v-btn text block href="/login">I have an account</v-btn>
                        </v-card-text>
                    </v-card>
                </v-form>
            </validation-observer>
        </v-col>
    </v-row>
</template>


<script>
import csrf from 'Mixins/csrf';
import validation from 'Mixins/validation';
import sitesettings from 'Mixins/sitesettings';
import {email, required, min} from 'vee-validate/dist/rules';
import {extend} from 'vee-validate';

export default {
    name: "PRegister",

    mixins: [csrf, validation, sitesettings],
    props: {
        route: {
            required: true,
            type: String
        },
        defaultIdentifier: {
            required: false,
            type: String,
            default: null
        },
        defaultRemember: {
            required: false,
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            credentials: {
                identifier: null,
                password: null,
                password_confirmation: null
            },
            loading: false
        }
    },
    created() {
        this.credentials.identifier = this.defaultIdentifier;
        this.useRules({
            email,
            required,
            min
        });
        extend('password_confirmed', {
            params: ['target'],
            validate(value, { target }) {
                return value === target;
            },
            message: 'Password confirmation does not match'
        });
    },
    mounted() {
        this.setServerErrors(this.$refs.observer);
    },
    methods: {
        submit(event) {
            event();
            alert('hi');
            this.loading = true;
            this.$refs.form.submit();
        }
    }
}
</script>

<style scoped>

</style>
