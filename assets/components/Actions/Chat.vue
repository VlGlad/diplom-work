<template>
    <div class="container py-5 px-4">
        <div class="row rounded-lg overflow-hidden shadow">
            <!-- Users box-->
            <Left />
            <!-- Chat Box-->
            <Blank v-if="$curConvId == -1" :key="$curConvId" />
            <template v-else>
                <Right />
            </template>
        </div>
    </div>

</template>

<script>
    import Left from "../Left/Left"; // Похоже не распознается изменение переменно, попробовать forceUpdate, или сделать локальную переменную, или перенести сюда Right
    import Right from "../Right/Right";
    import Blank from "../Right/Blank";
    
    export default {
        components: {Left, Right, Blank},
        watch: {
            $curConvId() {
                console.log('$curConvId has changed');
            }
        },
        beforeRouteLeave (to, from , next) {
            this.$curConvId = -1;
            next();
        }
    }
</script>