<template>
  <div style="padding-top:5px" ref="formContainer" class="horiz-scroll">
    <b-table
      show-empty
      stacked="md"
      :items="campaignPreviewList"
      :fields="fields"
      :current-page="currentPage"
      :per-page="perPage"
      :filter="filter"
      :sort-by.sync="sortBy"
      :tbody-tr-class="rowClass"
    >

      <template slot="isAllowCombine" slot-scope="row">
        {{ row.value=="1" ? 'Yes' : 'No' }}
      </template>
      
      <template slot="isActive" slot-scope="row">
        {{ row.value=="1" ? 'Yes' : 'No' }}
      </template>

      <template slot="isSiteWide" slot-scope="row">
        {{ row.value=="1" ? 'Yes' : 'No' }}
      </template>

      <template slot="discountType" slot-scope="row">
        {{ row.value=="$"? 'Amount':row.value=="%"? 'Percent':'' }}
      </template>

      <template slot="actions" slot-scope="row">

        <!-- <div  v-if="row.item.camID !=  winner"> -->
        <b-button size="sm" @click="row.toggleDetails" variant="light" v-b-tooltip.hover title="Show details" class="btn-outline-active" v-if="row.detailsShowing">
           <font-awesome-icon icon="chevron-up" /></b-button>
        </b-button>

        <b-button size="sm" @click="row.toggleDetails" variant="light" v-b-tooltip.hover title="Show details" class="btn-outline-active" v-else>
           <font-awesome-icon icon="chevron-down" /></b-button>
        </b-button>
      <!--  </div>
     <div v-else>
        <span style="margin:10px 10px 10px 5px; font-size: 25px; color: #ffb300;"><font-awesome-icon icon="award"/></span>
      </div> -->
      </template>



      

      <template slot="row-details" slot-scope="row">
        <b-card :id="row.item.camID" style="border: 0px solid rgba(0,0,0,.125);">
           <b-row>
 
              <b-col sm="12" md="6" lg="6" xl="6">              
                <h6 class="custom-txt">Categories</h6>
                <ul style="list-style: none; width:100%;">

                  <li style="margin-top: -30px;">
                    <b-badge variant="light" style="margin:0px; width:100%;">
                    <p style="width:100%; float:left">&nbsp;</p>
                  </b-badge>
                  </li>

                  <li v-for="(value, key) in campaignCatDetail[row.item.camID]" :key="key">
                  <b-badge variant="light" style="margin:0px; width:100%">
                    <p style="width:100%; float:left" :title="'level:'+value['catID']+'-'+value['level']">{{ value['CatName'] }}</p>
                  </b-badge>
                  </li>
                </ul>

                <h6 class="custom-txt">Coupon Codes</h6>
                <ul style="list-style: none; width:100%">
                  <li style="margin-top: -30px;">
                    <b-badge variant="light" style="margin:0px; width:100%;">
                    <p style="width:100%; float:left">&nbsp;</p>
                  </b-badge>
                  </li>                  
                  <li v-for="(value, key) in campaignDiscDetail[row.item.camID]" :key="key">
                    <b-badge variant="light" style="margin:0px; width:100%">
                      <p style="width:18%; float:left">{{ value['discountCode'] }} </p>
                      <p style="width:20%; float:left">{{ value['numberOfUse'] }} </p>
                    </b-badge>                    
                  </li>
                </ul>

                <h6 class="custom-txt">Locations</h6>
                <ul style="list-style: none; width:100%">

                  <li style="margin-top: -30px;">
                    <b-badge variant="light" style="margin:0px; width:100%;">
                    <p style="width:100%; float:left">&nbsp;</p>
                  </b-badge>
                  </li>   

                  <li v-for="(value, key) in campaignCounDetail[row.item.camID]" :key="key">
                    <b-badge variant="light" style="margin:0; width:100%">
                      <p style="width:100%; float:left">{{ value['name'] }} {{ value['code'] }} </p>
                    </b-badge>                    
                  </li>

                </ul>


              </b-col>

              <b-col sm="12" md="6" lg="6" xl="6">                  
                <h6 class="custom-txt">Products</h6>
                <ul style="list-style: none; width:100%">

                  <li style="margin-top: -30px;" v-if="campaignProdDetail[row.item.camID]">
                    <b-badge variant="light" style="margin:0px; width:100%;">
                    <p style="width:55%; float:left"></p>
                    <p style="width:15%; float:left; text-align:right; padding-right:20px">Reg P</p>
                    <p style="width:15%; float:left; text-align:right; padding-right:20px">Less</p>
                    <p style="width:15%; float:left; text-align:right; padding-right:20px">Sales</p>
                  </b-badge>
                  </li>

                  <li v-for="(value, key) in campaignProdDetail[row.item.camID]" :key="key">
                  <b-badge variant="light" style="margin:0px; width:100%">
                    <p style="width:10%; float:left">{{ value['prodID'] }} </p>
                    <p style="width:45%; float:left">{{ value['ProdName'] }} </p>
                    <p style="width:15%; float:left; text-align:right; padding-right:20px">{{value['Price']}} </p>
                    <p style="width:15%; float:left; text-align:right; padding-right:20px">{{formatPrice(value['actualDiscount'])}} </p>
                    <p style="width:15%; float:left; text-align:right; padding-right:20px">{{formatPrice(value['salesPrice'])}} </p>
                  </b-badge>
                </li>
           
                </ul>
              </b-col>

<!--               <b-col sm="12" md="6" lg="6" xl="6">               
                <h6 class="custom-txt">Coupon Codes</h6>
                <ul style="list-style: none; width:100%">
                  <li style="margin-top: -30px;">
                    <b-badge variant="light" style="margin:0px; width:100%;">
                    <p style="width:100%; float:left">&nbsp;</p>
                  </b-badge>
                  </li>                  
                  <li v-for="(value, key) in campaignDiscDetail[row.item.camID]" :key="key">
                    <b-badge variant="light" style="margin:0px; width:100%">
                      <p style="width:18%; float:left">{{ value['discountCode'] }} </p>
                      <p style="width:20%; float:left">{{ value['numberOfUse'] }} </p>
                    </b-badge>                    
                  </li>
                </ul>
              </b-col>

              <b-col sm="12" md="6" lg="6" xl="6">               
                <h6 class="custom-txt">Locations</h6>
                <ul style="list-style: none; width:100%">

                  <li style="margin-top: -30px;">
                    <b-badge variant="light" style="margin:0px; width:100%;">
                    <p style="width:100%; float:left">&nbsp;</p>
                  </b-badge>
                  </li>   

                  <li v-for="(value, key) in campaignCounDetail[row.item.camID]" :key="key">
                    <b-badge variant="light" style="margin:0; width:100%">
                      <p style="width:100%; float:left">{{ value['name'] }} {{ value['code'] }} </p>
                    </b-badge>                    
                  </li>

                </ul>
              </b-col> -->

            </b-row>
        </b-card>
      </template>
    </b-table>
<!--     <b-row>
      <b-col md="10" class="my-1">
        <b-pagination
          v-model="currentPage"
          :total-rows="totalRows"
          :per-page="perPage"
          class="my-0"
        ></b-pagination>
      </b-col>

      <b-col md="2" class="my-1">
        <b-form-group label-cols-sm="3" label="" class="mb-0">
          <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
        </b-form-group>
      </b-col>
    </b-row>
    <b-modal :id="infoModal.id" :title="infoModal.title" ok-only @hide="resetInfoModal">
      <pre>{{ infoModal.content }}</pre>
    </b-modal>   --> 
  </div>
</template>


<script>
  import {showErrorMsg, showSuccessMsg, getSavedCamCategory, getSavedCamProducts, getSavedCamCountries, getSavedCamDiscounts} from '../../helper';
  import axios from "axios";
  import _ from 'lodash';
  import { ModelSelect, MultiSelect } from 'vue-search-select';
  import CampaignUpdateModal from './CampaignUpdateModal.vue';
  
  export default {
    components: {ModelSelect, MultiSelect, CampaignUpdateModal},
    data() {
      return {
        campaignPreviewList: [],
        categoriesPreviewList: [],
        productsPreviewList: [],
        discountsPreviewList: [],
        countriesPreviewList: [],

        fields: [
          { key: 'camID', label: 'CamID'},
          { key: 'name', label: 'Campaign Name'},
          { key: 'description', label: 'Description'},
          { key: 'startDate', label: 'Start Date'},
          { key: 'endDate', label: 'End Date'},
          { key: 'discountType', label: 'Discount Type'},
          { key: 'discountAmount', label: 'Discount Amount'},
          { key: 'coupons', label: 'Coupon Codes'},
          { key: 'isAllowCombine', label: 'Combine Coupons', sortable: true},
          { key: 'locations', label: 'Locations'},
          { key: 'isSiteWide', label: 'IsSiteWide'},
          { key: 'isActive', label: 'IsActive'}
          // { key: 'actions', label: 'Actions' }
        ],

        totalRows: 1,
        currentPage: 1,
        perPage: 15,
        pageOptions: [5, 10, 15],
        sortBy: null,
        sortDesc: true,
        sortDirection: 'asc',
        filter: null,
        infoModal: {
          id: 'info-modal',
          title: '',
          content: ''
        },
        campaignList: [],
        campaignListTmp: [],
        campaignCatByLevel: [],
        campaignCatByCamID: [],
        campaignProdByCamID: [],
        campaignCounByCamID: [],
        campaignDiscByCamID: [],
        campaignCatDetail: [],
        campaignProdDetail: [],
        campaignCounDetail: [],
        campaignDiscDetail: [],
        prodArray:[],
        winner:''
      }
    },
    computed: {
      sortOptions() {
        return this.fields
          .filter(f => f.sortable)
          .map(f => {
            return { text: f.label, value: f.key }
          })
      }
    },
    watch : {
    }, 
    created() {
      this.$root.$on("campaign-preview-list",(campaignsPreviewList, categoriesPreviewList, productsPreviewList, discountsPreviewList, countriesPreviewList)=>{

        this.campaignPreviewList = campaignsPreviewList;
        this.totalRows = this.campaignPreviewList.length;
        this.campaignPreviewList[0]['_showDetails'] = true;

        this.winner = this.campaignPreviewList[0]['camID'];

        this.categoriesPreviewList = categoriesPreviewList; 
        this.productsPreviewList = productsPreviewList;
        this.discountsPreviewList = discountsPreviewList;
        this.countriesPreviewList = countriesPreviewList;
        
        this.campaignCatDetail = _(this.categoriesPreviewList).reduce(function(catSource, cam) {
          catSource[cam.camID] = catSource[cam.camID] || [];
          catSource[cam.camID].push(cam);
          return catSource;
        }, {});

        // this.campaignProdDetail = _(this.productsPreviewList).reduce(function(prodSource, cam) {
        this.campaignProdDetail = _(productsPreviewList).reduce(function(prodSource, cam) {
          prodSource[cam.camID] = prodSource[cam.camID] || [];
          prodSource[cam.camID].push(cam);
          return prodSource;
        }, {});
        
        this.campaignDiscDetail = _(this.discountsPreviewList).reduce(function(counSource, cam) {
          counSource[cam.camID] = counSource[cam.camID] || [];
          counSource[cam.camID].push(cam);
          return counSource;
        }, {});

        this.campaignCounDetail = _(this.countriesPreviewList).reduce(function(discSource, cam) {
          discSource[cam.camID] = discSource[cam.camID] || [];
          discSource[cam.camID].push(cam);
          return discSource;
        }, {});

        // for(let i = 0; i < prodIDs.length; i++ )
        // {
        //   if(i==0)
        //     this.sortBy = 'SP'+prodIDs[i];

        //   this.fields.push({key: 'RP'+prodIDs[i], label: 'RP'+prodIDs[i], sortable: true, sortDirection: 'asc'});
        //   this.fields.push({key: 'SP'+prodIDs[i], label: 'SP'+prodIDs[i], sortable: true, sortDirection: 'asc'});
        // }
        
        // this.fields.push({key: 'actions', label: ''});

      });
    }, 

    mounted() {
    },
    methods: {
      init(){
      },
      info(item, index, button) {
        this.infoModal.title = `Row index: ${index}`
        this.infoModal.content = JSON.stringify(item, null, 2)
        this.$root.$emit('bv::show::modal', this.infoModal.id, button)
      },
      resetInfoModal() {
        this.infoModal.title = ''
        this.infoModal.content = ''
      },
      onFiltered(filteredItems) {
        this.totalRows = filteredItems.length
        this.currentPage = 1
      },
      resetModal() {
        this.name = ''
        this.nameState = null
      },
      hideModal() {
        this.$refs['campaign-modal'].hide()
      },
      formatPrice(d) {
        if(isNaN(d))
            return 0;
        else 
            return parseFloat(d).toFixed(2);
      },
      rowClass(item, type) {
        console.log(JSON.stringify(item));
        if (!item) return
        //if (item.camID === this.campaignPreviewList[0]['camID']) return 'table-info'
      },
      subTotal(d){
        return this.campaignProdDetail[d].reduce((sum, c_data) => {
          let s = Math.round((parseFloat(c_data.actualDiscount)+sum)*100)/100; 
          return s;
        }, 0)
       }


    }
  };
</script>

<style scoped>
p {
    margin-top: 3px;
    margin-bottom: -2px;
}

.custom-txt {
  width: auto;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  -webkit-transition: all 0.35s ease;
  -moz-transition: all 0.35s ease;
  -o-transition: all 0.35s ease;
  -ms-transition: all 0.35s ease;
  transition: all 0.35s ease;
  padding: 3px;
  background-color: #dcf7ff;
  color: #6b6b6b;
}
.header-txt {
  float:left; overflow:hidden; white-space: nowrap;
}

.badge-light {
  color: #888888;
  background-color: #ffffff00;
  border-bottom: 1px solid #f3f3f3 !important;
}

table, th, td {
  border: 0px solid #fff !important;
}

.table td, .table th {
  border-top: 0px solid #dee2e6 !important;
  border-bottom: 1px solid #dee2e6 !important;
}

.badge {
  border-radius: 0rem;
}

</style>
