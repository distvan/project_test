<template>
    <div class="pager">
        <ul>
            <li><a @click.prevent="firstPage()" href="#">First</a></li>
            <li><a @click.prevent="previousPage()" href="#">Previous</a></li>
            <li>
                <select @change="pageChange()" v-model="currentPage">
                    <option v-for="n in range(from, to)" :value="n">{{ n }}</option>
                </select>
            </li>
            <li><a @click.prevent="nextPage()" href="#">Next</a></li>
            <li><a @click.prevent="lastPage()" href="#">Last</a></li>
        </ul>
    </div>
</template>

<script type="text/ecmascript-6">

    export default{
        props: ['current', 'total', 'from', 'to'],
        data(){
            return {
                currentPage: 0
            }
        },
        watch:{
            'current':{
                handler(value){
                    this.currentPage = value
                }
            }
        },
        methods: {
            range(min, max){
                var items = []
                var indx=0
                for(var i = min;i <= max; i++)
                {
                    items[indx]=i
                    indx++
                }
                return items
            },
            firstPage(){
                this.$emit('setCurrentPage', 0)
            },
            previousPage(){
                if(this.current > 0){
                    this.$emit('setCurrentPage', this.currentPage - 1)
                }
            },
            nextPage(){
                if(this.current < this.to){
                    this.$emit('setCurrentPage', this.currentPage + 1)
                }
            },
            lastPage(){
                this.$emit('setCurrentPage', this.to)
            },
            pageChange(){
                this.$emit('setCurrentPage', this.currentPage)
            }
        }
    }
</script>

<style>
    .pager{
        margin-left: auto;
        margin-right: auto;
        width: 400px;
    }
    .pager li{
        list-style-type: none;
        float: left;
        padding-left: 10px;
    }
    .pager input[list="pages"]{
        width: 40px;
        text-align: center;
    }
</style>