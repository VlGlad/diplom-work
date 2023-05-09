<template>
    <div class="container py-5 px-4">

        <div class="container add_logout">
        <div>
            <router-link to="/app/feed">Лента</router-link>
            <router-link to="/app/add">Добавить фото</router-link>
            <router-link to="/app/search">Поиск</router-link>
            <router-link to="/app/chat">Чат</router-link>
            <a role="button" class="btn btn-danger btn-lg" href="/logout">Выйти</a>
            <router-view></router-view>
        </div>
        </div>

    </div>

</template>

<script>
    
    export default {
    }
</script>