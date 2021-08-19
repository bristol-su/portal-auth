<template>
    <p-submit-form method="post" :action="route" :schema="form">

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
            return this.$tools.generator.form.newForm()
                .withGroup(this.$tools.generator.group.newGroup()
                    .withField(this.$tools.generator.field.text('identifier')
                        .label(this.identifier)
                        .hint('Enter your ' + this.identifierKey)
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
