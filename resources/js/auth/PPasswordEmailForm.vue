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
                method="POST"
                :action="$tools.routes.named.path('password.email')"
                v-slot="{invalid}">
                <v-card>
                    <v-card-title class="justify-center">
                        <span class="primary--text">{{$translator.translate('Reset Password')}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-alert type="success" v-if="status !== null && status !== ''">
                            {{ status }}
                        </v-alert>
                        <csrf-token></csrf-token>

                        <validation-provider
                            v-slot="{ errors }"
                            name="identifier"
                            rules="required">

                            <v-text-field
                                :label="$tools.settings.site.get('Authentication.Attributes.IdentifierLabel')"
                                id="identifier"
                                name="identifier"
                                v-model="identifier"
                                prepend-icon="mdi-account"
                                :error-messages="errors"
                                type="text"
                                :autofocus="!$tools.validation.server.has('identifier')"
                            ></v-text-field>
                        </validation-provider>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="primary" block type="submit" aria-label="Send Password Reset Link"
                               :disabled="invalid"
                               :loading="loading">
                            <v-icon>mdi-arrow-right</v-icon>
                        </v-btn>
                    </v-card-actions>
                    <v-card-text>
                        <v-btn text block href="/login">Take me back</v-btn>
                    </v-card-text>
                </v-card>
            </p-form>
        </v-col>
    </v-row>
</template>

<script>
import validation from 'Mixins/validation';
import PForm from '../../components/form/PForm';
import {required} from 'vee-validate/dist/rules';

export default {
    name: "PPasswordEmailForm",
    components: {PForm},
    data() {
        return {
            identifier: null,
            loading: false
        }
    },
    created() {
        this.useRules({
            required
        });
    },
    mixins: [validation],
    props: {
        status: {
            required: false,
            type: String,
            default: null
        },
    }
}
</script>

<style scoped>

</style>
