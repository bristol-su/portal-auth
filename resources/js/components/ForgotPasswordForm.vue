<template>
    <p-submit-form method="post" :action="route" :schema="form" button-text="Send reset email">

    </p-submit-form>
</template>

<script>
export default {
    name: "ForgotPasswordForm",
    props: {
        route: {
            required: true, type: String
        },
        identifier: {
            required: true, type: String
        },
        identifierKey: {
            required: true, type: String
        }
    },
    computed: {
        form() {
            let identifierField = this.$tools.generator.field.text('identifier')
                .label(this.identifier)
                .hint('Enter your ' + this.identifierKey)
                .required(true);
            if(this.identifierKey === 'email') {
                identifierField.autocomplete('email');
            }
            return this.$tools.generator.form.newForm()
                .withGroup(this.$tools.generator.group.newGroup()
                    .withField(identifierField)
                )
                .generate()
                .asJson();
        }
    }
}
</script>

<style scoped>

</style>
