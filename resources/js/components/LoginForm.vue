<template>
    <p-submit-form method="post" :action="route" :schema="form" button-text="Login">

    </p-submit-form>
</template>

<script>
export default {
    name: "LoginForm",
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
                        .hint('Enter the ' + this.identifierKey + ' you used to register.')
                        .required(true)
                    ).withField(this.$tools.generator.field.password('password')
                        .label('Password')
                        .hint('Enter your password')
                        .required(true)
                    ).withField(this.$tools.generator.field.checkbox('remember')
                        .label('Remember Me'))
                )
                .generate()
                .asJson();
        }
    }
}
</script>

<style scoped>

</style>
