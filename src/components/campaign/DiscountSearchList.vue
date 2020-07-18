<template>
    <div @mouseleave="hideCounSelection">
        <b-container fluid>
          <div>
            
            <b-input-group class="mt-3">
              <b-input-group-text slot="append"><font-awesome-icon icon="search" /></b-input-group-text>
              <b-form-input class="LoginInput" size="lg" placeholder="Search" v-model="tableData.search" @input="getDiscounts()"  @click="getDiscounts()" ></b-form-input>
            </b-input-group>

          </div>
          <div style="position:absolute; width:100%; padding-right:30px; z-index:1000">
            <div style="background-color:#fff;">            
            <DiscountSearchColumn :columns="columns" v-bind:class="{'coun-result-box-hide': hideResultBox}" class="coun-result-box">
                <tbody>
                    <tr v-for="(discount, index) in discounts" :key="discount.disID">
                      <td v-on:click="selectData(discount)">{{discount.description}} </td>
                   </tr>
                </tbody>
            </DiscountSearchColumn>
          </div>
          </div>
        </b-container>
     </div>
</template>

<script>
  import {showErrorMsg, showSuccessMsg} from '../../helper';
  import axios from "axios";
  import _ from 'lodash';
  import { ModelSelect, MultiSelect } from 'vue-search-select';
  import DiscountSearchColumn from './DiscountSearchColumn.vue';

  export default {
    components: {ModelSelect, MultiSelect, DiscountSearchColumn},
    data() {
      let columns = [
          {width: '100%', label: 'Click to select...', name: 'DiscName' },
      ];
      return {
        discounts: [],
        columns: columns,
        tableData: {
            draw: 0,
            length: 10,
            search: '',
            dir: 'desc',
        },
        hideResultBox: true,
        fromWhich: ''
      }
    },
    computed: {
    },
    watch : {
    }, 
    created() {
      this.$root.$on("on-hide-coun-search",(d)=>{
        this.hideCounSelection();
      });
      this.$root.$on("on-load-coun-search",(d)=>{
         console.log(d)
         this.fromWhich = d;
      });      
      this.$root.$on("refresh-discount",(d)=>{
        this.getDiscounts();
      });      
    }, 
    mounted() {
    },
    methods: {
      getDiscounts(url = process.env.API_URL+"api/discountsbyname") {
        this.showCounSelection();
        const that = this;
        this.tableData.draw++;
        axios.get(url, {params: this.tableData})
            .then(response => {
                let data = response.data;
                console.log('prod list: '+JSON.stringify(data.posts));
                this.$root.$emit("on-coun-search",data.posts.length);
                if (this.tableData.draw == data.draw) {
                    this.discounts = data.posts;
                }
            })
            .catch(errors => {
                console.log(errors);
            });
      },
      selectData(d)
      {
        this.$root.$emit('discount-selected', d)
      },
      hideCounSelection()
      {
        this.hideResultBox = true; 
      },
      showCounSelection()
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
  .coun-result-box {
    display: block;
  }
  .coun-result-box-hide {
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