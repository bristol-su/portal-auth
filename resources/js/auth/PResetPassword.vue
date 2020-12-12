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
                            <span class="primary--text">Reset Password</span>
                        </v-card-title>
                        <v-card-text>
                            <input type="hidden" :value="csrf" name="_token" id="_token"/>
                            <input type="hidden" :value="token" name="token" id="token"/>
                            <input type="hidden" :value="identifier" name="identifier" id="identifier"/>

                            <validation-provider
                                v-slot="{ errors }"
                                name="identifier-display"
                                rules="required">

                                <v-text-field
                                    :label="getSetting(settingKeys.authentication.identifier.label)"
                                    id="identifier-display"
                                    name="identifier-display"
                                    v-model="identifier"
                                    disabled
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
                            <v-btn color="primary" block type="submit" aria-label="Reset Password" :disabled="invalid"
                                   :loading="loading">
                                <v-icon>mdi-arrow-right</v-icon>
                            </v-btn>
                        </v-card-actions>
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
import {extend} from 'vee-validate';
import {email, min, required} from 'vee-validate/dist/rules';

export default {
    name: "PResetPassword",
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
    mixins: [csrf, validation, sitesettings],
    props: {
        route: {
            required: true,
            type: String
        },
        token: {
            required: true,
            type: String,
        },
        identifier: {
            required: true,
            type: String,
        },
    },
    created() {
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
    }
}
</script>

<style scoped>

</style>
