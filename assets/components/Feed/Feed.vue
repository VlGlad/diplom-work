<template>
    
    <div class="container-fluid">
    

    <div class="container" style="text-align: center; width: 500px;">
            <div class="container">
                <img class="feed_img" src="" alt="">
                <div class="container reactions_container">
                    <button role="button" reaction-target="" style="margin: 5px;">like 22</button>
                    <div class="container comment_container">
                        <textarea name="comment" reaction-target="" placeholder="Написать комментарий"></textarea>
                        <button onclick="sendComment(this)">Send</button>
                    </div>
                    <div class="container">
                            <p>КОММЕНТ</p>
                    </div>
                </div>
            </div>
            
    </div>
</div>

</template>

<script>
    export default {
        data: () => ({
            feed_content: {}
        }),
        mounted() {
            fetch(`/app/getfeed`)
                .then(response => response.json())
                .then(data => (console.log(data)));
        }
    }
</script>