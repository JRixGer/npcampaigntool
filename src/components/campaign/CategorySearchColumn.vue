<template>
 <div class="fixed-table fixed-table-container h-w horiz-scroll"> 
  <table class="table table-bordered table-hover">
    <thead v-bind:class="{'cat-result-show': searchResult > 0, 'cat-result': searchResult < 1}">
       <tr>
        <th v-for="column in columns" :key="column.name" :id="column.name"
            :style="'width:'+column.width+';'+'cursor:pointer;'">
            {{column.label}}
        </th>
        <th @click="Hide()" style="cursor:pointer;">Hide</th>
      </tr>
    </thead>
    <slot></slot>
  </table>
  </div>
</template>

<script>
  
  export default {
    props: ['columns'],
    mounted() {
    },
    data() {
      return {
        searchResult: 0
      }
    },
    created() {
      this.$root.$on("on-cat-search",(n)=>{
        this.searchResult = n;
      });
    },
    methods:{
      Hide(){
        this.$root.$emit('on-hide-cat-search', true)
      }
    }
  };

</script>
<style  scoped>
.table thead th {
  background-color: #fff;
  font-size: 12px;
  font-style: italic;
  font-weight: 500;
  color: #c1c1c1;
  border: 0px solid #fff; 
}

.cat-result-show {
  display: block;
}
.cat-result {
  display: none;
}
.table th, .table td {
  border-top: 0px solid #fff; 
}
</style>
