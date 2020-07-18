<template>
  <div style="padding-top:5px" ref="formContainer">
    <b-table
      show-empty
      stacked="md"
      :items="campaignList"
      :fields="fields"
      :current-page="currentPage"
      :per-page="perPage"
      :filter="filter"
      :sort-by.sync="sortBy"
      :tbody-tr-class="rowClass"
    >
      <!-- <template slot="name" slot-scope="row">
        {{ row.value.first }} {{ row.value.last }}
      </template> -->

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
        <div style="text-align: center">
        <b-button v-if="row.detailsShowing" size="sm" @click="getSelectedCampaign(row.item, 'FILL DETAIL', row.index); row.toggleDetails()" variant="light" v-b-tooltip.hover title="Show details" class="btn-outline-active" ref="showDetails">
           <font-awesome-icon icon="chevron-up" /></b-button>
        </b-button>


        <b-button v-else size="sm" @click="getSelectedCampaign(row.item, 'FILL DETAIL', row.index); row.toggleDetails()" variant="light" v-b-tooltip.hover title="Show details" class="btn-outline-active" ref="showDetails">
           <font-awesome-icon icon="chevron-down" /></b-button>
        </b-button>

        <!-- <b-button size="sm" @click="row.toggleDetails" variant="light" v-b-tooltip.hover title="Show details" class="btn-outline-active" v-if="row.detailsShowing">
           <font-awesome-icon icon="chevron-up" /></b-button>
        </b-button>

        <b-button size="sm" @click="row.toggleDetails" variant="light" v-b-tooltip.hover title="Show details" class="btn-outline-active" v-else>
           <font-awesome-icon icon="chevron-down" /></b-button>
        </b-button> -->



        <b-button size="sm" variant="light" v-b-tooltip.hover title="Update this campaign" @click="getSelectedCampaign(row.item, 'UPDATE')" class="btn-outline-active">
           <font-awesome-icon icon="pen-alt" /></b-button>
        </b-button>

        <b-button size="sm" variant="light" v-b-tooltip.hover title="Copy this campaign" @click="showMsgConfirmation(row.item, row.index, 'campaign', 'COPY')" class="btn-outline-active">
           <font-awesome-icon icon="copy" /></b-button>
        </b-button>

        <b-button size="sm" variant="light" v-b-tooltip.hover title="Campaign preview" @click="getSelectedCampaign(row.item, 'PREVIEW')" class="btn-outline-active">
           <font-awesome-icon icon="search" /></b-button>
        </b-button>

        <b-button size="sm" @click="showMsgConfirmation(row.item, row.index, 'campaign', 'ARCHIVE')" variant="light" v-b-tooltip.hover title="Archive this campaign" class="mr-1 btn-outline-active">
          <font-awesome-icon icon="archive" /></b-button>
        </b-button>
      </div>
      </template>

      <template slot="row-details" slot-scope="row">
        <b-card :id="row.item.camID" style="border: 0px solid rgba(0,0,0,.125);">
           <b-row>
              
              <b-col sm="12" md="6" lg="6" xl="6">            
                <h6 class="custom-txt">Categories</h6>
                <ul style="list-style: none; width:100%; margin-bottom:15px">
                  <li style="margin-top: -30px;" v-if="campaignCatDetail[row.item.camID]">
                    <b-badge variant="light" style="margin:0px; width:100%; background-color:rgba(255, 255, 255, 0)">
                    <p style="width:70%; float:left"></p>
                    <p style="width:10%" class="header-txt" v-b-tooltip.hover title="Exclude/Include Sitewide">
                      ExcS
                    </p> 
                    <p style="width:10%" class="header-txt" v-b-tooltip.hover title="Exclude/Include Discount" >
                      ExcD
                    </p> 
                    <p style="width:10%" class="header-txt" v-b-tooltip.hover title="Exclude from campaign" >
                      Del
                    </p>
                  </b-badge>
                  </li>

                  <li v-for="(value, key) in campaignCatDetail[row.item.camID]" :key="key">
                  <b-badge variant="light" style="margin:0px; width:100%">
                    <p style="width:70%; float:left" :title="'level:'+value['level']">{{ value['CatName'] }}</p>
                    <p style="width:10%; float:left">
                      <input type="checkbox" title="Exclude/Include Sitewide" :checked="value['isExcluded']==1? 'checked' : ''" :id="'cs'+value['camCatID']" @click="setIsExcluded($event, value['camCatID'], 'campaign category', 'sitewide')">
                    </p> 
                    <p style="width:10%; float:left">
                      <input type="checkbox" title="Exclude/Include Discount" :checked="value['isExcluded']==1? 'checked' : ''" :id="'cd'+value['camCatID']" @click="setIsExcluded($event, value['camCatID'], 'campaign category', 'discount')">
                    </p> 
                    <p style="width:10%; float:left"><font-awesome-icon icon="times" class="btn-outline-active-del-icon" title="Exclude from campaign" @click="showMsgConfirmation(value['camCatID'], key, 'campaign category', 'DELETE', row.item.camID)"/></p>
                  </b-badge>

                  </li>
                </ul>
              </b-col>

              <b-col sm="12" md="6" lg="6" xl="6">     
                <h6 class="custom-txt">Products</h6>
                <ul style="list-style: none; width:100%; margin-bottom:15px">
                  <li style="margin-top: -30px;" v-if="campaignProdDetail[row.item.camID]">
                    <b-badge variant="light" style="margin:0px; width:100%; background-color:rgba(255, 255, 255, 0)">
                    <p style="width:50%; float:left"></p>
                    <p style="width:20%; float:left; text-align:right; padding-right:20px">Price</p>
                    <p style="width:10%" class="header-txt" v-b-tooltip.hover title="Exclude/Include Sitewide">
                      ExcS
                    </p>   
                    <p style="width:10%" class="header-txt" v-b-tooltip.hover title="Exclude/Include Discount">
                      ExcD
                    </p>                     
                    <p style="width:10%" class="header-txt" v-b-tooltip.hover title="Exclude from campaign">
                      Del
                    </p>
                  </b-badge>
                  </li>
                  <li v-for="(value, key) in campaignProdDetail[row.item.camID]" :key="key">
                  <b-badge variant="light" style="margin:0px; width:100%">
                    <p style="width:50%; float:left">{{ value['ProdName'] }} </p>
                    <p style="width:20%; float:left; text-align:right; padding-right:20px">{{value['Price']}} </p>
                    <p style="width:10%; float:left"><input type="checkbox" title="Exclude/Include Sitewide" :checked="value['isExcluded']==1? 'checked' : ''" :id="'p'+value['camProdID']" @click="setIsExcluded($event, value['camProdID'], 'campaign product', 'sitewide')"></p>                    
                    <p style="width:10%; float:left"><input type="checkbox" title="Exclude/Include Discount" :checked="value['isExcluded']==1? 'checked' : ''" :id="'p'+value['camProdID']" @click="setIsExcluded($event, value['camProdID'], 'campaign product', 'discount')"></p>    
                    <p style="width:10%; float:left"><font-awesome-icon icon="times" class="btn-outline-active-del-icon" @click="showMsgConfirmation(value['camProdID'], key, 'campaign product', 'DELETE', row.item.camID)" title="Exclude from campaign"/></p>
                  </b-badge>                  
                </li>
                </ul>
              </b-col>

              <b-col sm="12" md="6" lg="6" xl="6"> 
                <h6 class="custom-txt">Coupon Codes</h6>
                <ul style="list-style: none; width:100%; margin-bottom:15px">
                  <li style="margin-top: -30px;" v-if="campaignDiscDetail[row.item.camID]">
                    <b-badge variant="light" style="margin:0px; width:100%; background-color:rgba(255, 255, 255, 0)">
                    <p style="width:80%; float:left"></p>
                    <p style="width:10%" class="header-txt" v-b-tooltip.hover title="Number of use">
                      Use
                    </p>
                    <p style="width:10%" class="header-txt" v-b-tooltip.hover title="Delete from campaign">
                      Del
                    </p>
                  </b-badge>
                  </li>                  
                  <li v-for="(value, key) in campaignDiscDetail[row.item.camID]" :key="key">
                    <b-badge variant="light" style="margin:0px; width:100%">
                      <p style="width:80%; float:left">{{ value['discountCode'] }} </p>
                      <p style="width:10%; float:left">{{ numberOfUse(value['numberOfUse']) }} </p>
                      <p style="width:10%; float:left"><font-awesome-icon icon="times" class="btn-outline-active-del-icon" title="Delete from campaign"  @click="showMsgConfirmation(value['camDiscID'], key, 'campaign discount', 'DELETE', row.item.camID)"/></p>
                    </b-badge>                    
                  </li>
                </ul>
              </b-col>

              <b-col sm="12" md="6" lg="6" xl="6">
                <h6 class="custom-txt">Locations</h6>
                <ul style="list-style: none; width:100%; margin-bottom:15px">
                  <li style="margin-top: -30px;" v-if="campaignCounDetail[row.item.camID]">
                    <b-badge variant="light" style="margin:0px; width:100%; background-color:rgba(255, 255, 255, 0)">
                    <p style="width:90%; float:left"></p>
                    <p style="width:10%;" class="header-txt" v-b-tooltip.hover title="Delete from campaign">
                      Del
                    </p>
                  </b-badge>
                  </li>                     
                  <li v-for="(value, key) in campaignCounDetail[row.item.camID]" :key="key">
                    <b-badge variant="light" style="margin:0px; width:100%">
                      <p style="width:90%; float:left">{{ value['name'] }} {{ value['code'] }} </p>
                      <p style="width:10%; float:left"><font-awesome-icon icon="times" class="btn-outline-active-del-icon" title="Delete from campaign"  @click="showMsgConfirmation(value['camCounID'], key, 'campaign country', 'DELETE', row.item.camID)"/></p>
                    </b-badge>                    
                  </li>
                </ul>
              </b-col>

            </b-row>
        </b-card>
      </template>
    </b-table>
    <b-row>
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
    <!-- Info modal -->
    <b-modal :id="infoModal.id" :title="infoModal.title" ok-only @hide="resetInfoModal">
      <pre>{{ infoModal.content }}</pre>
    </b-modal>   
    <CampaignUpdateModal></CampaignUpdateModal>
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
        fields: [
          { key: 'camID', label: 'Cam ID', sortable: true, sortDirection: 'desc'},
          { key: 'name', label: 'Name', sortable: true},
          { key: 'description', label: 'Description', sortable: true},
          { key: 'startDate', label: 'Start Date', sortable: true},
          { key: 'endDate', label: 'End Date', sortable: true},
          { key: 'discountType', label: 'Discount Type', sortable: true},
          { key: 'discountAmount', label: 'Discount Amount', sortable: true},
          { key: 'coupons', label: 'Coupon Codes', sortable: true},
          { key: 'isAllowCombine', label: 'Combine Coupons', sortable: true},
          { key: 'locations', label: 'Locations', sortable: true},
          { key: 'isSiteWide', label: 'Is Site Wide'},
          { key: 'isActive', label: 'Is Active'},
          { key: 'actions', label: 'Actions' }
        ],
        totalRows: 1,
        currentPage: 1,
        perPage: 15,
        pageOptions: [5, 10, 15],
        sortBy: null,
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,
        infoModal: {
          id: 'info-modal',
          title: '',
          content: ''
        },
        campaignList: [],

        campaignListAll: [],
        campaignListWithCoupons: [],
        campaignListWithoutCoupons: [],

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
        prodID:'',
        campaignsPreviewWithProducts:[],
        campaignsPreviewList:[],
        campaignsPreviewWithProducts:[],
        categoriesPreviewList:[],
        productsPreviewList:[],
        discountsPreviewList:[],
        countriesPreviewList:[],
        filterType: 'All'
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
      this.$root.$on("campaign-list",(campaignList, campaignCatByLevel, _showDetailsYN = null, filterType)=>{
        this.filterType = filterType;
        if(_showDetailsYN)
        {
          this.campaignList = campaignList;
          this.campaignList[0]['_showDetails'] = true;
          this.campaignCatByLevel = campaignCatByLevel;
          this.totalRows = campaignList.length;          
          this.getSelectedCampaign(this.campaignList[0], 'FILL DETAIL');
        }
        else
        {

          this.campaignListAll = campaignList;
          this.campaignListWithCoupons = campaignList.filter(c => c.coupons !== null);
          this.campaignListWithoutCoupons = campaignList.filter(c => c.coupons === null);
  
          if(this.filterType == "All")
            this.campaignList = this.campaignListAll;
          else if(this.filterType == "Without coupons")
            this.campaignList = this.campaignListWithoutCoupons;
          else if(this.filterType == "With coupons")
            this.campaignList = this.campaignListWithCoupons;

          this.campaignCatByLevel = campaignCatByLevel;
          this.totalRows = campaignList.length;

        }
      });
      
      this.$root.$on("campaign-list-filtered",(filterType)=>{
        this.filterType = filterType;

        if(this.filterType == "All")
          this.campaignList = this.campaignListAll;
        else if(this.filterType == "Without coupons")
          this.campaignList = this.campaignListWithoutCoupons;
        else if(this.filterType == "With coupons")
          this.campaignList = this.campaignListWithCoupons;

        this.totalRows = this.campaignList.length;

      });

    }, 
    mounted() {
    },
    methods: {
      init(){
      },
      setIsExcluded(e, id, whichTable, whichPart = null)
      {
        var that = this;
        let isExcluded = 0;
        if (e.target.checked) 
          isExcluded = 1;
        
        var formData = new FormData();
        formData.append('id', id);           
        formData.append('isExcluded', isExcluded); 
        formData.append('whichTable', whichTable); 
        formData.append('whichPart', whichPart); 
        formData.append('ACTION', 'isExcluded'); 

        axios.post(process.env.API_URL+"api/updatecampaign", formData).then(function(response){
          if (response.data.error){
            that.$toastr.error(response.data[1], 'Error', {timeOut: 1000});
          }else{
            that.$toastr.success(response.data[1], 'Success', {timeOut: 1000});              
          }
        });
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
      getSelectedCampaign(campaign, action, i = null) {

        if(action == "FILL DETAIL"){
          this.campaignList[i]._rowVariant="active";
          if($('#'+campaign.camID).is(':visible')){
            this.campaignList[i]._rowVariant="";
            return;
          }
        }

        let loader = this.$loading.show({
          //container: this.fullPage ? null : this.$refs.formContainer,
          container: null,
          canCancel: true,
          onCancel: this.onCancel,
        });

        var that = this;
        axios.all([
          axios({ method: "GET", "url": process.env.API_URL+"api/campaigncategories" }).then(result => {
            this.campaignCatByCamID = result.data.posts;
          }, error => {
             console.error(error);
             this.campaignCatByCamID = [];
          }),
          axios({ method: "GET", "url": process.env.API_URL+"api/campaignproducts" }).then(result => {
            this.campaignProdByCamID = result.data.posts;
          }, error => {
             console.error(error);
             this.campaignProdByCamID = [];
          }),
          axios({ method: "GET", "url": process.env.API_URL+"api/campaigncountries" }).then(result => {
            this.campaignCounByCamID = result.data.posts;

            console.log(this.campaignCounByCamID);

          }, error => {
             console.error(error);
             this.campaignCounByCamID = [];
          }), 
          axios({ method: "GET", "url": process.env.API_URL+"api/campaigndiscounts" }).then(result => {
            this.campaignDiscByCamID = result.data.posts;

          }, error => {
             console.error(error);
             this.campaignDiscByCamID = [];
          }),     

          // axios({ method: "GET", "url": process.env.API_URL+"api/campaigncategories?camid="+campaign.camID }).then(result => {
          //   this.campaignCatByCamID = result.data.posts;
          // }, error => {
          //    console.error(error);
          //    this.campaignCatByCamID = [];
          // }),
          // axios({ method: "GET", "url": process.env.API_URL+"api/campaignproducts?camid="+campaign.camID }).then(result => {
          //   this.campaignProdByCamID = result.data.posts;
          // }, error => {
          //    console.error(error);
          //    this.campaignProdByCamID = [];
          // }),
          // axios({ method: "GET", "url": process.env.API_URL+"api/campaigncountries?camid="+campaign.camID }).then(result => {
          //   this.campaignCounByCamID = result.data.posts;
          // }, error => {
          //    console.error(error);
          //    this.campaignCounByCamID = [];
          // }),          
        ])
        .then(axios.spread(function (acct, perms) {
          that.campaignCatDetail = _(that.campaignCatByCamID).reduce(function(catSource, cam) {
            catSource[cam.camID] = catSource[cam.camID] || [];
            catSource[cam.camID].push(cam);
            return catSource;
          }, {});


          that.campaignProdDetail = _(that.campaignProdByCamID).reduce(function(prodSource, cam) {
            prodSource[cam.camID] = prodSource[cam.camID] || [];
            prodSource[cam.camID].push(cam);
            return prodSource;
          }, {});


          that.campaignCounDetail = _(that.campaignCounByCamID).reduce(function(counSource, cam) {
            counSource[cam.camID] = counSource[cam.camID] || [];
            counSource[cam.camID].push(cam);
            return counSource;
          }, {});

          that.campaignDiscDetail = _(that.campaignDiscByCamID).reduce(function(discSource, cam) {
            discSource[cam.camID] = discSource[cam.camID] || [];
            discSource[cam.camID].push(cam);
            return discSource;
          }, {});

          // let catByCamID = [];
          // if(that.campaignCatByCamID)
          //   catByCamID = getSavedCamCategory(that.campaignCatByCamID.filter(c => c.camID === campaign.camID), that.campaignCatByLevel);  
        
          // let prodByCamID = [];
          // if(that.campaignProdByCamID)
          //   prodByCamID = getSavedCamProducts(that.campaignProdByCamID.filter(c => c.camID === campaign.camID)); 

          // let counByCamID = [];
          // if(that.campaignCounByCamID)
          //   counByCamID = getSavedCamCountries(that.campaignCounByCamID.filter(c => c.camID === campaign.camID));  
        
          if(action == "UPDATE" || action == "COPY" || action == "FILL DETAIL")
          {
            let catByCamID = [];
            if(that.campaignCatDetail[campaign.camID])
              catByCamID = getSavedCamCategory(that.campaignCatDetail[campaign.camID], that.campaignCatByLevel);  
          
            let prodByCamID = [];
            if(that.campaignProdDetail[campaign.camID])
              prodByCamID = getSavedCamProducts(that.campaignProdDetail[campaign.camID]); 

            let discByCamID = [];
            if(that.campaignDiscDetail[campaign.camID])
              discByCamID = getSavedCamDiscounts(that.campaignDiscDetail[campaign.camID]);  

            let counByCamID = [];
            if(that.campaignCounDetail[campaign.camID])
              counByCamID = getSavedCamCountries(that.campaignCounDetail[campaign.camID]);  

            if(action == "UPDATE")
              that.$root.$emit('campaign-update', campaign, catByCamID, prodByCamID, discByCamID, counByCamID)  // passed variables are main for select options      
            else if(action == "COPY")
              that.copyCampaign(campaign, catByCamID, prodByCamID, discByCamID, counByCamID)

          }
          else if(action == "PREVIEW")
          {
            let catsInCampaign = that.campaignCatByCamID.filter(c => c.camID === campaign.camID); 
            let prodsInCampaign = that.campaignProdByCamID.filter(c => c.camID === campaign.camID); 
            that.showPreview(campaign, catsInCampaign, prodsInCampaign);
          }
          else if(action == "FILL DETAIL")
          {

          }
          loader.hide()
        }));     
      },
      showMsgConfirmation(campaign, index, tableName, action, el = false) {
        
        let id = null;
        if(tableName == "campaign category" || tableName == "campaign product" || tableName == "campaign discount" || tableName == "campaign country")
          id = campaign;
        else 
          id = campaign.camID;
        
        this.$bvModal.msgBoxConfirm('Please confirm that you want to '+action+' '+tableName+' id: '+id, {
          title: 'Confirm '+action,
          size: 'sm',
          buttonSize: 'sm',
          okVariant: 'danger',
          okTitle: 'YES',
          cancelTitle: 'NO',
          footerClass: 'p-2',
          hideHeaderClose: false,
          centered: true
        })
        .then(value => {
          if(value && action == 'ARCHIVE' && tableName == 'campaign')
          {
            this.campaignList.splice(index, 1);
            this.deleteCampaign(campaign);
          }
          else if(value && action == 'COPY')
          {
            this.getSelectedCampaign(campaign, action);
          }          
          else if(value && action == 'DELETE' && tableName == 'campaign category')
          {
            this.campaignCatDetail[el].splice(index, 1);
            this.deleteCampaignDetail(id, tableName);
          }  
          else if(value && action == 'DELETE' && tableName == 'campaign product')
          {
            this.campaignProdDetail[el].splice(index, 1);
            this.deleteCampaignDetail(id, tableName);
          }            
          else if(value && action == 'DELETE' && tableName == 'campaign discount')
          {
            this.campaignDiscDetail[el].splice(index, 1);
            this.deleteCampaignDetail(id, tableName);
          }  
          else if(value && action == 'DELETE' && tableName == 'campaign country')
          {
            this.campaignCounDetail[el].splice(index, 1);
            this.deleteCampaignDetail(id, tableName);
          }                      
        })
        .catch(err => {
          // An error occurred
        })
      },
      deleteCampaign(campaign) {
        var that = this
        var formData = new FormData();
        formData.append('campaign', JSON.stringify(campaign)); 
        formData.append('action', 'campaign'); 
        axios.post(process.env.API_URL+"api/deletecampaign", formData).then(function(response){
            if (response.data.error){
            }else{
              console.log(JSON.stringify(campaign));
              that.$root.$emit('campaign-deletion', true)
            }
          });        
      },
      deleteCampaignDetail(id, tableName) {
        var that = this
        var formData = new FormData();
        formData.append('id', id); 
        formData.append('tableName', tableName); 
        formData.append('action', 'detail'); 
        axios.post(process.env.API_URL+"api/deletecampaign", formData).then(function(response){
            if (response.data.error){
            }else{
              //that.$root.$emit('campaign-deletion', true)
            }
          });  
      },      
      copyCampaign(campaign, catByCamID, prodByCamID, discByCamID, counByCamID) {
        var that = this
        var formData = new FormData();
        formData.append('campaign', JSON.stringify(campaign)); 
        formData.append('campaignCategories', JSON.stringify(catByCamID)); 
        formData.append('campaignProducts', JSON.stringify(prodByCamID)); 
        formData.append('campaignDiscounts', JSON.stringify(discByCamID)); 
        formData.append('campaignCountries', JSON.stringify(counByCamID)); 
        axios.post(process.env.API_URL+"api/copycampaign", formData).then(function(response){
            if (response.data.error){
            }else{
              that.$root.$emit('campaign-copying', true)
              that.$toastr.success(response.data[1], 'Success', {timeOut: 1000});
            }
          });        
      }, 
      showPreview(campaign, category, product) {
        this.$root.$emit('campaign-preview-list-show', true); 
        this.$root.$emit('campaign-selected', campaign.name); 

        var that = this;
        let loader = this.$loading.show({
          container: this.fullPage ? null : this.$refs.formContainer,
          canCancel: true,
          onCancel: this.onCancel,
        });

        let para = {};
        para['campaign'] = JSON.stringify(campaign);
        para['category'] = JSON.stringify(category);
        para['product'] = JSON.stringify(product);
        axios.all([
          axios({method: "GET", 
            "url": process.env.API_URL+"api/campaignpreview",
            params: para}).then(result => {

            this.campaignsPreviewList = result.data.campaigns;
            this.categoriesPreviewList = result.data.categories;
            this.productsPreviewList = result.data.products;
            this.discountsPreviewList = result.data.discounts;
            this.countriesPreviewList = result.data.countries;
          }, error => {
             console.error(error);
          }),
          
        ])
        .then(axios.spread(function (acct, perms) {
          that.$root.$emit('campaign-preview-list', that.campaignsPreviewList, that.categoriesPreviewList, that.productsPreviewList, that.discountsPreviewList, that.countriesPreviewList);
          loader.hide()
        }));
        

        // let campaignProdByCamIDFiltered = this.campaignProdByCamID.filter(c => c.prodID === prodID); 
        // let camIDs = [];

        // for(let i = 0; i < campaignProdByCamIDFiltered.length; i++)
        //     camIDs.push(campaignProdByCamIDFiltered[i]['camID']);

        // this.campaignList = this.campaignList.filter(c => (jQuery.inArray(c.name, camIDs) !== -1)); 
        // this.totalRows = this.campaignList.length;

        // // category
        // this.campaignCatByCamID = _(this.campaignCatByCamID.filter(c => (jQuery.inArray(c.camID, camIDs) !== -1))).reduce(function(catSource, cam) {
        //   catSource[cam.camID] = catSource[cam.camID] || [];
        //   catSource[cam.camID].push(cam);
        //   return catSource;
        // }, {});
        
        // //product            
        // this.campaignProdByCamID = _(this.campaignProdByCamID.filter(c => (jQuery.inArray(c.camID, camIDs) !== -1))).reduce(function(prodSource, prod) {
        //   prodSource[prod.camID] = prodSource[prod.camID] || [];
        //   prodSource[prod.camID].push(prod);
        //   return prodSource;
        // }, {});
  
        // // country
        // this.campaignCounByCamID = _(this.campaignCounByCamID.filter(c => (jQuery.inArray(c.camID, camIDs) !== -1))).reduce(function(counSource, coun) {
        //   counSource[coun.camID] = counSource[coun.camID] || [];
        //   counSource[coun.camID].push(coun);
        //   return counSource;
        // }, {});

        //this.$root.$emit('campaign-relevant-info', this.campaignList, this.campaignCatByCamID, this.campaignProdByCamID, this.campaignCounByCamID, prodID)
      },
      formatPrice(value) {
        let val = (value/1).toFixed(2).replace('.', ',')
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
      },
      numberOfUse(d) {
        if(d > 0)
          return d;
        else
          return "";
      },
      rowClass(item, type) {
        if (!item) return
        if (item.isActive != 1) return 'table-fade-this'
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

</style>
<style>
.table-fade-this {
  color: #dedede;
}
</style>