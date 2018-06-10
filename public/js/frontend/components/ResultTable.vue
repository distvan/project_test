<template>
    <div>
        <div class="filter">
            <input class="name" size="40" v-model="filterName" type="text" placeholder="Type book title or author name" />
            <input type="checkbox" v-model="filterAdult" value="1" /> is Adult?
            <input type="button" @click="doFilter" value="Filter" />
            <bounce-loader :loading="loading"></bounce-loader>
        </div>
        <pager :current="currentPage" :total="totalPage" :from="fromPage" :to="toPage" v-on:setCurrentPage="setPage"></pager>
        <table class="result-table">
            <thead>
                <tr>
                    <td>#num</td>
                    <td>Book Title</td>
                    <td>Category</td>
                    <td>Authors</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, idx) in tableData">
                    <td>{{ (idx+1) + (perPage * currentPage) }}.</td>
                    <td><span :class="{adult: item.is_adult==1}">{{ item.title }}</span></td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.authors }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <pager :current="currentPage" :total="totalPage" :from="fromPage" :to="toPage" v-on:setCurrentPage="setPage"></pager>
    </div>
</template>

<script type="text/ecmascript-6">

    import Pager from './Pager.vue'
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue'

    export default{
        data(){
            return {
                tableData: [],
                filterName: '',
                filterAdult: false,
                totalPage: 0,
                fromPage: 0,
                toPage: 0,
                perPage: 1,
                currentPage: 0,
                loading: false
            }
        },
        created(){
            this.loadTable()
        },
        methods: {
            loadTable(){
                this.loading = true
                var that = this
                var fn = (this.filterName == '') ? ' ' : this.filterName
                var adultInput = this.filterAdult ? 1 : 0;
                this.$http.get('/api/books/' + fn + '/' + adultInput + '/' + this.currentPage).then(response => {
                    that.tableData = response.data.result
                    that.totalPage = parseInt(response.data.total) || 0
                    that.perPage = parseInt(response.data.per_page) || 0
                    if(that.perPage)
                    {
                        that.toPage = Math.floor(that.totalPage/that.perPage)
                    }
                    this.loading = false
                }, error => {
                    console.log(error)
                    this.loading = false
                })
            },
            doFilter(){
                this.currentPage = 0
                this.loadTable()
            },
            setPage(n){
                this.currentPage = n
                this.loadTable()
            }
        },
        components:{
            'pager': Pager,
            'bounce-loader': PulseLoader
        }
    }
</script>

<style>
    .result-table{
        border-collapse: collapse;
        width: 100%;
        margin-top:50px;
    }
    .result-table td {
        border-top: 1px solid #dee2e6;
    }
    .result-table th,td{
        padding: .5rem;
    }
    .result-table thead td{
        background-color: #efefef;
        color: #222222;
        font-weight: bold;
        padding: 0px 5px;
    }
    .result-table .adult{
        color: red;
    }
    .filter{
        background-color: #e7efef;
        padding: 5px;
        vertical-align: middle;
    }
    .filter input[type="button"]{
        background-color: #555555;
        border: none;
        color: white;
        padding: 5px 15px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 13px;
    }
    .filter .name{
        height: 22px;
    }
    .v-spinner{
        display:inline;
        float:right;
        margin-right: 20%;
        margin-top: 5px;
    }
</style>