<template>
    <v-row
        justify="center"
        align="center"
        class="fill-height">
        <v-col
            cols="12"
            sm="8"
            md="4">
            <p-form
                v-slot="{ invalid }"
                :action="$tools.routes.named.path('login')"
                method="POST">
                <v-card>
                    <v-card-title class="justify-center">
                        <span class="primary--text"><translate text="Sign In"></translate></span>
                    </v-card-title>
                    <v-card-text>
                        <csrf-token></csrf-token>

                        <validation-provider
                            v-slot="{ errors }"
                            name="identifier"
                            rules="required">

                            <v-text-field
                                :label="$tools.settings.site.get('Authentication.Attributes.IdentifierLabel')"
                                id="identifier"
                                name="identifier"
                                v-model="credentials.identifier"
                                prepend-icon="mdi-account"
                                :error-messages="errors"
                                type="text"
                                :autofocus="!$tools.validation.server.has('identifier')"
                            ></v-text-field>
                        </validation-provider>

                        <v-text-field
                            id="password"
                            label="Password"
                            name="password"
                            v-model="credentials.password"
                            prepend-icon="mdi-lock"
                            type="password"
                        ></v-text-field>

                        <v-switch
                            id="remember"
                            name="remember"
                            class="ma-0"
                            :input-value="credentials.remember"
                            @change="credentials.remember = $event"
                            label="Remember me"
                            :value="credentials.remember"
                        ></v-switch>

                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="primary" block type="submit" aria-label="Login" :disabled="invalid"
                               :loading="loading">
                            <v-icon>mdi-arrow-right</v-icon>
                        </v-btn>
                    </v-card-actions>
                    <v-card-text>
                        <v-btn text block href="/register">I'm new here</v-btn>
                        <v-btn text block href="/password/reset">I've forgotten my password</v-btn>
                    </v-card-text>
                </v-card>
            </p-form>
        </v-col>
    </v-row>
    <!--                                                       class="col-md-4 col-form-label text-md-right">{{ ucfirst(siteSetting('authentication.registration_identifier.identifier')) }}</label>-->
    <!--                                                    <a class="btn btn-link" href="{{ route('password.request') }}">-->
    <!--                                                        {{ __('Forgot Your Password?') }}-->
    <!--                                                    </a>-->
    <!--                                                    @endif-->
    <!--                                                </div>-->
    <!--                                            </div>-->
    <!--                                        </form>-->
    <!--                                    </div>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                            @if(is_array(siteSetting('thirdPartyAuthentication.providers', [])) && count(siteSetting('thirdPartyAuthentication.providers', [])) > 0)-->
    <!--                            <div class="col-md-3" style="border-left: 2px solid black">-->
    <!--                                <div class="row">-->
    <!--                                    <div class="col-md-12">-->
    <!--                                        <h5>Or login with...</h5>-->
    <!--                                    </div>-->
    <!--                                </div>-->
    <!--                                <div class="row">-->
    <!--                                    <div class="col-md-12">-->
    <!--                                        <social-login :providers="{{json_encode(siteSetting('thirdPartyAuthentication.providers'))}}"></social-login>-->
    <!--                                    </div>-->
    <!--                                </div>-->
    <!--                            </div>-->
    <!--                            @endif-->
    <!--                        </div>-->

    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
</template>

<script>
import validation from 'Mixins/validation';
import {required, email} from 'vee-validate/dist/rules';
import PForm from 'Components/form/PForm';

export default {
    name: "PLogin",
    components: {PForm},
    mixins: [validation],
    data() {
        return {
            credentials: {
                identifier: this.$tools.validation.oldInput.get('identifier', null),
                password: null,
                remember: this.$tools.validation.oldInput.get('remember', 'false') === 'true'
            },
            loading: false
        }
    },
    created() {
        this.useRules({
            email,
            required
        });
    }
}
</script>

<style scoped>

</style>
