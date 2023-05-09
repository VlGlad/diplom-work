<template>
    <!-- Typing area -->
    <div>
        <form class="bg-light" v-on:submit.prevent="sendMessage">
        <div class="input-group">
            <input type="text" placeholder="Введите сообщение" v-model="content" aria-describedby="button-addon2" class="form-control rounded-0 border-0 py-4 bg-light">
            <input type="hidden">
            <div class="input-group-append">
                <button id="button-addon2" type="submit" class="btn btn-link"> <i class="fa fa-paper-plane"></i></button>
            </div>
        </div>
    </form>
    </div>
</template>

<script>
    export default {
        data: () => ({
            content: ''
        }),
        methods: {
            sendMessage() {
                // event.preventDefault(); @keyup.enter="sendMessage($event)"
                this.$store.dispatch("POST_MESSAGE", {
                    conversationId: this.$curConvId,
                    content: this.content
                }).
                    then(() => {
                        this.content = ''
                })
            }
        }
    }
</script>
