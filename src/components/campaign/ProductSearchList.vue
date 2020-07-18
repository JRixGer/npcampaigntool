<template>
    <div @mouseleave="hideProdSelection">
        <b-container fluid>
          <div>
            
            <b-input-group class="mt-3">
              <b-input-group-text slot="append"><font-awesome-icon icon="search" /></b-input-group-text>
              <b-form-input class="LoginInput" size="lg" placeholder="Search" v-model="tableData.search" @input="getProducts()"  @click="getProducts()" ></b-form-input>
            </b-input-group>

          </div>
          <div style="position:absolute; width:100%; padding-right:30px; z-index:1000">
            <div style="background-color:#fff;">            
            <ProductSearchColumn :columns="columns" v-bind:class="{'prod-result-box-hide': hideResultBox}" class="prod-result-box">
                <tbody>
                    <tr v-for="(product, index) in products" :key="product.ProdID">
                      <td v-on:click="selectData(product)">{{product.ProdName}} [{{product.ProdID}}]</td>
                   </tr>
                </tbody>
            </ProductSearchColumn>
          </div>
          </div>
        </b-container>
     </div>
</template>


<script>
  import {showErrorMsg, showSuccessMsg, busySpin, stopSpin} from '../../helper';
  import axios from "axios";
  import _ from 'lodash';
  import { ModelSelect, MultiSelect } from 'vue-search-select';
  import ProductSearchColumn from './ProductSearchColumn.vue';

  export default {
    components: {ModelSelect, MultiSelect, ProductSearchColumn},
    data() {
      let columns = [
          {width: '100%', label: 'Click to select...', name: 'ProdName' },
      ];
      return {
        products: [],
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
      this.$root.$on("on-hide-prod-search",(n)=>{
        this.hideProdSelection();
      });
    }, 
    mounted() {
    },
    methods: {
      getProducts(url = process.env.API_URL+"api/productsbyname") {
        this.showProdSelection();
        const that = this;
        this.tableData.draw++;
        axios.get(url, {params: this.tableData})
            .then(response => {
                let data = response.data;

                console.log('prod list: '+JSON.stringify(data.posts));


                this.$root.$emit("on-prod-search",data.posts.length);
                if (this.tableData.draw == data.draw) {
                    this.products = data.posts;
                }
            })
            .catch(errors => {
                console.log(errors);
            });
      },
      selectData(d)
      {
        this.$root.$emit('product-selected', d)
      },
      hideProdSelection()
      {
        this.hideResultBox = true; 
      },
      showProdSelection()
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
  .prod-result-box {
    display: block;
  }
  .prod-result-box-hide {
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