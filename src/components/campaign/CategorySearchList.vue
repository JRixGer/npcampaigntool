<template>
    <div @mouseleave="hideCatSelection">
        <b-container fluid>
          <div>

            <b-input-group class="mt-3">
              <b-input-group-text slot="append"><font-awesome-icon icon="search" /></b-input-group-text>
              <b-form-input class="LoginInput" size="lg" placeholder="Search" v-model="tableData.search" @input="getCategories()"  @click="getCategories()" ></b-form-input>
            </b-input-group>

          </div>
          <div style="position:absolute; width:100%; padding-right:30px; z-index:1001">
            <div style="background-color:#fff;" id="searchBar">
            <CategorySearchColumn :columns="columns" v-bind:class="{'cat-result-box-hide': hideResultBox}" class="cat-result-box">
                <tbody>
                    <tr v-for="(category, index) in categories" colspan="2">
                      <td v-on:click="selectData(category)" v-html="category[4]"></td>
                   </tr>
                </tbody>
            </CategorySearchColumn>
          </div>
          </div>
        </b-container>
     </div>
</template>

<script>
  import {showErrorMsg, showSuccessMsg, splitData} from '../../helper';
  import axios from "axios";
  import _ from 'lodash';
  import { ModelSelect, MultiSelect } from 'vue-search-select';
  
  import CategorySearchColumn from './CategorySearchColumn.vue';


  export default {
    props:["fromWhich"],
    components: {ModelSelect, MultiSelect, CategorySearchColumn},
    data() {

      let columns = [
          {width: '98%', label: 'Click to select...', name: 'CatName' },
      ];

      return {
        categories: [],
        columns: columns,
        tableData: {
            draw: 0,
            length: 10,
            search: '',
            dir: 'desc',
        },
        hideResultBox: false
      }
    },
    computed: {
     
    },
    watch : {
    }, 
    created() {
      console.log('fromWhich '+this.fromWhich);
      this.$root.$on("on-hide-cat-search",(n)=>{
        this.hideCatSelection();
      });
    }, 
    mounted() {
    },
    methods: {
 
       getCategories(url = process.env.API_URL+"api/categoriesbyname") {
        this.showCatSelection();
        const that = this;
        this.tableData.draw++;

        axios.get(url, {params: this.tableData})
          .then(response => {
      
              let data = response.data;
              this.$root.$emit("on-cat-search",data.posts.length);

              if (this.tableData.draw == data.draw) {
                  
                  this.categories = splitData(data.posts, this.tableData.search);

                  console.log('data: '+JSON.stringify(this.categories))                      
              }
          })
          .catch(errors => {
              console.log(errors);
          });
      },
      selectData(d)
      {
        console.log(d)
        this.$root.$emit('category-selected', d)
      },
      hideCatSelection()
      {
        this.hideResultBox = true; 
      },
      showCatSelection()
      {
        this.hideResultBox = false;
      }
    }
  };
</script>
<style scoped>
  .container-fluid {
    padding-right: 0px; 
    padding-left: 0px; 
  }
  .cat-result-box {
    display: block;
  }
  .cat-result-box-hide {
    display: none !important;
  }
  .form-control-lg {
    padding: .5rem .7rem;
    font-size: 0.8rem;
  }
  .input-group-text {
    background-color: #ffffff;
    border-right: 1px solid #ced4da;
    border-bottom: 1px solid #ced4da;
    border-top: 1px solid #ced4da;
    border-left: 0px solid #ffffff !important;
  }
  .form-control {
    border-right: 0px solid #ffffff;
    border-bottom: 1px solid #ced4da;
    border-top: 1px solid #ced4da;
    border-left: 1px solid #ced4da !important;
  }
  .mt-3, .my-3 {
    margin-top: .1rem !important;
  }
</style>