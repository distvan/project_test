<template>
    <div class="top">
        <h2>Sample Project</h2>
        <resulttable></resulttable>
    </div>
</template>

<script type="text/ecmascript-6">
    import ResultTable from './components/ResultTable.vue'

    export default{
        components:{
            'resulttable': ResultTable
        }
    }
</script>

<style>
    .top{
        width: 50%;
        margin-left: auto;
        margin-right: auto;
    }
    .top h2{
        text-align: center;
    }
</style>