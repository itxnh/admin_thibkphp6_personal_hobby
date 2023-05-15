<template>
    <Key v-if="icon == 'Key'" />
    <List v-if="icon == 'List'" />
    <Setting v-if="icon == 'Setting'" />
    <Aim v-if="icon == 'Aim'" />
    <UserFilled v-if="icon == 'UserFilled'" />
    <Link v-if="icon == 'Link'" />
</template>
<script>
    export default {
        name: "Icon",
        props: {
            icon: {
                type: String,
                required: true
            }
        }
    };
</script>