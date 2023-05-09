<template>
    <div class="container">
        <form class="bg-light" v-on:submit.prevent="addPost">
            <div class="input-group">
                <input type="text" placeholder="Введите текст" v-model="content" aria-describedby="button-addon2" class="form-control rounded-0 border-0 py-4 bg-light">
                <input type="file" @change="setFilePath($event)">
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
            content: '',
            filePath: '',
        }),
        methods: {
            setFilePath(event){
                this.filePath = event.target.files[0];
            },
            addPost(){
                let formData = new FormData();
                formData.append('text_content', this.content);
                formData.append('file_url', this.filePath)

                return fetch(`/app/add`, {
                    method: "POST",
                    body: formData
                })
                    .then(result => result.json())
                    .then((result) => {
                        console.log(result);
                })
            }
        }
    }
</script>