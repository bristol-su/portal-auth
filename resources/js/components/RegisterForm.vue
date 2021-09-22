<template>
    <p-submit-form method="post" :action="route" :schema="form" :button-text="buttonText">

    </p-submit-form>
</template>

<script>
export default {
    name: "RegisterForm",
    props: {
        route: {
            required: true, type: String
        },
        identifier: {
            required: true, type: String
        },
        identifierKey: {
            required: true, type: String
        },
        buttonText: {
            required: true, type: String
        },
        identifierValue: {
            required: false, default: null
        }
    },
    computed: {
        form() {
            let identifierField = this.$tools.generator.field.text('identifier')
                .label(this.identifier)
                .hint('Enter your ' + this.identifierKey)
                .required(true)
                .value(this.identifierValue)
                .disabled(true);
            if(this.identifierKey === 'email') {
                identifierField.autocomplete('email');
            }
            return this.$tools.generator.form.newForm()
                .withGroup(this.$tools.generator.group.newGroup()
                    .withField(identifierField)
                    .withField(this.$tools.generator.field.password('password')
                        .label('Password')
                        .hint('Enter a secure password')
                        .required(true)
                    ).withField(this.$tools.generator.field.password('password_confirmation')
                        .label('Password Confirmation')
                        .hint('Re-enter your password')
                        .required(true)
                    )
                )
                .generate()
                .asJson();
        }
    }
}
</script>

<style scoped>

</style>
