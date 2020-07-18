<template>
    <div style="padding:20px" ref="formContainer">
        <b-card style="padding:5px" :title="viewTitle">
          <!-- <b-tabs card>
            <b-tab active> -->
    
            <!-- <template slot="title">
              {{viewTitle}}
            </template> -->

              <b-card-text>
                <b-container fluid>
                  <b-row>
                    <b-col md="8" style="margin-top:5px">                    
                      <span style="margin:5px">
                        <b-button v-if="viewType != 'processeddata'" variant="light" class="btn-outline-active" v-b-tooltip.hover title="Create a campaign" @click="loadCreateCampaign"><font-awesome-icon icon="plus"/></b-button>
                      </span>
                      <span style="margin:5px">
                        <b-button v-if="viewType != 'processeddata'" variant="light" @click="getCampaigns()" class="btn-outline-active" v-b-tooltip.hover title="Reset"><font-awesome-icon icon="redo-alt"/></b-button>
                        <b-button v-if="viewType == 'processeddata'" variant="light" @click="goBack()" class="btn-outline-active" v-b-tooltip.hover title="Back"><font-awesome-icon icon="arrow-left"/></b-button>
                      </span>
                      <span style="margin:5px" v-if="viewType != 'processeddata'" >
                        <b-button-group>
                          <b-button variant="light" class="btn-outline-active" v-b-tooltip.hover title="Filter with coupons" @click="showfiltered('With coupons')" ><font-awesome-icon icon="funnel-dollar" /></b-button>
                          <b-button variant="light" class="btn-outline-active" v-b-tooltip.hover title="Filter no coupons"  @click="showfiltered('Without coupons')" ><font-awesome-icon icon="filter" /></b-button>
                          <b-button variant="light" class="btn-outline-active" v-b-tooltip.hover title="Show all" @click="showfiltered('All')" >All</b-button>
                        </b-button-group>
                      </span>
                    </b-col>    
                    <b-col md="4"> 
                      <div>                   
                        <CampaignSearchList></CampaignSearchList>
                      </div>
                    </b-col>    
                    <b-col md="12" class="my-1" v-if="viewType == 'processeddata'">
                      <CampaignPreview></CampaignPreview>
                    </b-col>
                    <b-col md="12" class="my-1" v-else>
                      <CampaignList></CampaignList>
                      <b-modal
                        id="campaign-create-id"
                        ref="campaign-modal"
                        title="CREATE CAMPAIGN"
                        size="lg"
                        no-close-on-backdrop
                        no-fade
                      >
                      <form ref="form" @submit.stop.prevent="saveCampaign">
                      <div>
                      <form-wizard @on-complete="onComplete"
                      ref="wizard"
                      :start-index="0"
                      finish-button-text="Save"
                      color="#41a500">

                        <h4 slot="title"></h4>  

                        <tab-content title="CAMPAIGN"
                         icon="ti-files">

                          <b-row>
                            <b-col cols="3" style="margin-bottom:10px; text-align:left">
                              <b-form-checkbox v-model="campaign.isActive" name="check-button" switch>
                                Active
                              </b-form-checkbox>

                            </b-col>
                            <b-col cols="3" style="margin-bottom:10px; text-align:left">

                              <b-form-checkbox v-model="campaign.isSiteWide" name="check-button" switch>
                                Sitewide
                              </b-form-checkbox>  
                            </b-col>
                            <b-col cols="6" style="margin-bottom:10px; text-align:left">

                              <b-form-checkbox v-model="skipToLocation" name="check-button" switch @change="setSkip($event)">
                                Use Coupon Codes
                              </b-form-checkbox>  
                            </b-col>                            
                          </b-row>


                          <b-row>
                          <b-col cols="6">
                            <b-form-group
                              label="Name"
                              label-for="name-input"
                              invalid-feedback="Name is required"
                            >
                              <b-form-input
                                id="description-input"
                                v-model="$v.campaign.name.$model" :class="status($v.campaign.name)"
                                required
                              ></b-form-input>
                            </b-form-group>
                          </b-col>

                          <b-col cols="6">
                            <b-form-group
                              label="Description"
                              label-for="description-input"
                              invalid-feedback="Description is required"
                            >
                              <b-form-input
                                id="description-input"
                                v-model="$v.campaign.description.$model" :class="status($v.campaign.description)"
                                required
                              ></b-form-input>
                            </b-form-group>
                          </b-col>
                          </b-row>

                          <b-row>
                          <b-col cols="6">
                            <b-form-group
                              label="Start Date"
                              label-for="start-date-input"
                              invalid-feedback="Start Date is required"
                            >
                              <b-form-input
                                type="date"
                                id="start-date-input"
                                v-model="campaign.startDate"
                                required
                              ></b-form-input>
                            </b-form-group>
                          </b-col>

                          <b-col cols="6">
                            <b-form-group
                              label="End Date"
                              label-for="end-date-input"
                              invalid-feedback="End Date is required"
                            >
                              <b-form-input
                                type="date"
                                id="end-date-input"
                                v-model="campaign.endDate"
                                required
                              ></b-form-input>
                            </b-form-group>
                          </b-col>
                          </b-row>

                          <b-row>
                            <b-col cols="12">
                              <b-form-group
                                label="Category"
                                label-for="category-input"
                              >
                               <div class="custom-box"><div v-for="(cat, index) in campaignCategories" style="width:auto; float:left"><b-badge variant="info" style="margin:3px"><span v-html="cat.completeName"></span> <span><font-awesome-icon icon="times" @click="removeItem('Cat',index)" class="btn-outline-active-del"/></span></b-badge></div></div>
                              <CategorySearchList></CategorySearchList>
                              </b-form-group>
                            </b-col>
                            <b-col cols="12">
                              <b-form-group
                                label="Products"
                                label-for="products-input"
                              >
                              <div class="custom-box"><span v-for="(prod, index) in campaignProducts"><b-badge variant="info" style="margin:3px">{{prod.prodName}} [{{prod.prodID}}] <span><font-awesome-icon icon="times" @click="removeItem('Prod',index)" class="btn-outline-active-del"/></span></b-badge></span></div>
                              <ProductSearchList></ProductSearchList>
                              </b-form-group>
                            </b-col>
                          </b-row>
  
  
                         <b-row>
                            <b-col cols="6">
                              <b-form-group
                                label="Discount Type"
                                label-for="discount-type-input"
                                invalid-feedback="Discount type is required"
                              >
                              <b-form-select v-model="$v.campaign.discountType.$model" :options="discountTypeOptions" :class="status($v.campaign.discountType)"></b-form-select>
                              </b-form-group>
                            </b-col>

                            <b-col cols="6">
                              <b-form-group
                                label="Discount Amount"
                                label-for="discount-amount-input"
                                invalid-feedback="Discount amount is required"
                              >
                                <b-form-input
                                  id="discount-amount-input"
                                  v-model="$v.campaign.discountAmount.$model" :class="status($v.campaign.discountAmount)"
                                  required
                                ></b-form-input>
                              </b-form-group>
                            </b-col>
                          </b-row>
                        </tab-content>
                        
                        <tab-content title="COUPON CODES"
                         icon="ti-tag">
                          <b-row>
                            <b-col cols="6">
                              <h5>Selected Codes</h5>
                            </b-col>
                            <b-col cols="6" style="margin-bottom:10px; text-align:left">
                              <b-form-checkbox v-model="campaign.isAllowCombine" name="check-button" switch>
                                Allow coupons to be combined with other campaigns
                              </b-form-checkbox>
                            </b-col>

                            <b-col cols="12">
                              <b-form-group
                                label=""
                                label-for="siscount-input"
                              >
                              <div class="custom-box"><span v-for="(disc, index) in campaignDiscounts"><b-badge variant="info" style="margin:3px">{{disc.discountCode}} <font-awesome-icon icon="times" @click="removeItem('Disc',index)" class="btn-outline-active-del"/></b-badge></span></div>
                             <!--  <DiscountSearchList></DiscountSearchList> -->
                              </b-form-group>
                            </b-col>
                            <b-col cols="12">
                            <AddDiscount v-bind:fromWhere="fromWhere"></AddDiscount>
                            </b-col>
                          </b-row>
                        </tab-content>
                        <tab-content title="LOCATIONS"
                         icon="ti-location-pin">

                           <b-row>
                            <b-col cols="12" style="margin-bottom:20px">
                              <h5></h5>
                            </b-col>                            
                            <b-col cols="12" style="margin-bottom:20px">

                              <b-form-group label="">
                                <b-form-checkbox-group
                                  v-model="selectedCountries"
                                  :options="optionsCountries"
                                  switches
                                  stacked
                                  @change="processClicked($event)"
                                ></b-form-checkbox-group>
                              </b-form-group>

                            </b-col>
                          </b-row>
                        </tab-content>

                        <template slot="footer" slot-scope="{activeTabIndex, isLastStep, nextTab, prevTab, fillButtonStyle}">
                          <div class=wizard-footer-left>
                            
                            <button v-if="activeTabIndex > 0 &&  skipToLocation"
                                    @click="prevTab"
                                    type="button" 
                                    class="btn btn-secondary">
                              Back
                            </button>

                            <button v-else="activeTabIndex > 0 &&  !skipToLocation"
                                    @click="gotBackCampaign"
                                    type="button" 
                                    class="btn btn-secondary">
                              Back
                            </button>

                          </div>

                          <div class="wizard-footer-right">
                            <!-- <button v-if="!isLastStep" 
                                    @click="nextTab"
                                    type="button" 
                                    class="btn btn-success wizard-footer-right">
                              Next
                            </button> -->
                          

                            <button v-if="!isLastStep && skipToLocation" 
                                    @click="nextTab"
                                    type="button" 
                                    class="btn btn-success wizard-footer-right">
                              Next
                            </button>

                            <button v-else-if="!isLastStep && !skipToLocation" 
                                    @click="gotToLocation"
                                    type="button" 
                                    class="btn btn-success wizard-footer-right">
                              Next
                            </button>

                            <button v-else 
                                    @click="nextTab"
                                    type="button" 
                                    class="btn btn-primary">
                                  Save
                            </button>
                          </div>
                        </template>

                      </form-wizard>
                      </div>
                      </form> 
                        <div slot="modal-footer" class="w-100">
                          <!-- <p class="float-left">Modal Footer Content</p>
                          <b-button
                            variant="light"
                            size="sm"
                            class="float-right"
                            @click="hideModal"
                          >
                            Cancel
                          </b-button>
                          <b-button
                            type="submit"
                            variant="primary"
                            size="sm"
                            class="float-right"
                            style="margin-right:10px"
                            @click = "saveCampaign"
                          >
                            Save
                          </b-button> -->
                        </div>

                      </b-modal>
                      <!-- end of campaign modal -->
                    </b-col>
                  </b-row>
                </b-container>
               </b-card-text>
            <!-- </b-tab> -->
<!--  
            <b-tab title="Gift Cards">
              <b-card-text>
                <b-row>
                  <b-col md="6" class="my-1">
                     <b-button variant="light" title = "Create gift card"><font-awesome-icon icon="plus" /></b-button> 
                     hi anak i just sent $100, im sorry my capability is mininmum for now, i hope it can help a litle bit    
                  </b-col>
                </b-row>
              </b-card-text>
            </b-tab>
           <b-tab title="Products List (test)">
              <b-card-text>
              </b-card-text>
            </b-tab> -->

          <!-- </b-tabs> -->
        </b-card>
    </div>
</template>

<script>
  import {showErrorMsg, showSuccessMsg, handleDup, setDiscountsOption} from '../../helper';
  import axios from "axios";
  import _ from 'lodash';
  import { ModelSelect, MultiSelect } from 'vue-search-select';
  import { required, minLength, between, alphaNum, helpers} from 'vuelidate/lib/validators';
  const alpha = helpers.regex('alpha', /^[a-zA-Z]*$/);
  const number = helpers.regex('number', /^\d*$/);
  const phoneNumber = helpers.regex('phoneNumber', /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/);
  const alphaNumSpace = helpers.regex('alphaNumSpace', /^[A-Za-z,\d\s]+$/);
  const alphaSpace = helpers.regex('alphaSpace', /^[A-Za-z,\s]+$/);

  import CampaignList from './CampaignList.vue';
  //import DiscountList from './DiscountList.vue';
  import ProductSearchList from './ProductSearchList.vue';
  import CategorySearchList from './CategorySearchList.vue';
  import DiscountSearchList from './DiscountSearchList.vue';
  import CampaignSearchList from './CampaignSearchList.vue';
  import AddDiscount from './AddDiscount.vue';
  import CampaignPreview from './CampaignPreview.vue';
  

  export default {
    components: {ModelSelect, MultiSelect, CampaignList, CategorySearchList, ProductSearchList, DiscountSearchList, AddDiscount, CampaignSearchList, CampaignPreview},
    data() {
      return {
        viewType: 'rawdata',
        viewTitle: 'Campaign',
        campaignList:[],
        campaignCatList: [],
        campaignProdList: [],
        campaignCounList:[],
        discountList: [],
        campaign: {
          camID: '',
          name: '',
          description: '',
          startDate: '',
          endDate: '',
          discountCode: '', 
          discountType: '',           
          discountAmount: '', 
          isAllowCombine:false,
          isSiteWide: '', 
          isActive: true
        },
        campaignCategories: [], 
        campaignCategoriesTmp: [], 
        campaignProducts: [], 
        campaignProductsTmp: [], 
        campaignCountries: [],
        campaignCountriesTmp: [],
        campaignDiscounts: [],                         
        discountsOptions:[],     
        discountTypeOptions:[
          { value: '$', text: 'Amount' },
          { value: '%', text: 'Percent' }
        ],
        tableData: {
            draw: 0,
            length: 10,
            search: '',
            dir: 'desc',
        },
        optionsCountries: [
          { text: 'Sweden', value: 'Sweden' },
          { text: 'Denmark', value: 'Denmark' },
          { text: 'Finland', value: 'Finland'},
          { text: 'Norway', value: 'Norway' }
        ],
        selectedCountries: [],
        campaignDiscounts: [], 
        errors:{},
        skipToLocation: false,
        fromWhere: "ADD",
        filterType: "All"
      }
    },
    validations: {
      campaign: {
          name: {
                required,
                minLength: minLength(2)
            },
          description: {
                required,
                minLength: minLength(2)
            },
          discountType: {
                required
            },
          discountAmount: {
                required,
                between: between(0, 999)
            }                          
      },      
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
        "campaign.discountType":function(){
          if(this.campaign.discountType == "$")
            this.selectedCountries = [];
        },
        "campaign.discountAmount":function(){
          if(this.campaign.discountType == "%" && this.campaign.discountAmount >= 50)
            this.$toastr.warning('Are you sure on this discount amount '+this.campaign.discountAmount+this.campaign.discountType+'?', 'Warning', {timeOut: 1500});
        }        
    }, 
    created() {
      this.$root.$on("campaign-copying",(e)=>{
        this.getCampaigns();     
      }); 
      this.$root.$on("campaign-deletion",(e)=>{
        this.getCampaigns();     
      });  
      this.$root.$on("campaign-updated",(e)=>{
        this.getCampaigns();     
      });              
      this.$root.$on("category-selected",(e)=>{
        this.campaignCategoriesTmp = this.campaignCategories.filter(c => c.catID === e[1]); 
        if(this.campaignCategoriesTmp.length == 0)
          this.campaignCategories.push({'catName': e[0], 'catID': e[1], 'completeName': e[2], 'level': e[3]});
      });  
      this.$root.$on("product-selected",(e)=>{
        this.campaignProductsTmp = this.campaignProducts.filter(c => c.prodID === e['ProdID']); 
        if(this.campaignProductsTmp.length == 0)
          this.campaignProducts.push({'prodID': e['ProdID'], 'prodName': e['ProdName']});
      });  

      // this.$root.$on("discount-selected",(e)=>{
      //   this.campaignDiscountsTmp = this.campaignDiscounts.filter(c => c.discountCode === e['discountCode']); 
      //   if(this.campaignDiscountsTmp.length == 0)
      //     this.campaignDiscounts.push({'discountCode': e['discountCode'],'description': e['description'],'numberOfUse': e['numberOfUse']});

      // });  

      this.$root.$on("refresh-discount-add",(e)=>{
        this.campaignDiscountsTmp = this.campaignDiscounts.filter(c => c.discountCode === e['discountCode']); 
        if(this.campaignDiscountsTmp.length == 0)
          this.campaignDiscounts.push({'discountCode': e['discountCode'],'description': e['description'],'numberOfUse': e['numberOfUse']});

      });  

      // this.$root.$on("campaign-selected",(d)=>{
      //   this.getCampaigns();     
      // }); 

      this.$root.$on("preview-campaign-selected",(d)=>{
        this.getCampaigns(d);     
      });

      this.$root.$on("preview-campaign-prod-selected",(d)=>{
        this.campaignPreview(d);     
      });      

      this.$root.$on("campaign-preview-list-show",(d, e)=>{
        this.viewType = 'processeddata';
        this.viewTitle = 'Preview';
      });
    }, 
    mounted() {
      this.getCampaigns();
    },
    methods: {
      showfiltered(d)
      {
        this.filterType = d;
        this.$root.$emit("campaign-list-filtered",this.filterType);
      },
      goBack()
      {
        this.viewType = 'rawdata';
        this.viewTitle = 'campaign'; 
        this.getCampaigns();       
      },
      loadCreateCampaign() {
        this.$refs.wizard.reset();
        this.skipToLocation = false;
        this.campaignDiscounts = [];
        this.campaignCategories = [];
        this.campaignProducts = [];
        this.$refs['campaign-modal'].show();

        this.campaign.name = '';
        this.campaign.description = '';
        this.campaign.startDate = '';
        this.campaign.endDate = '';
        this.campaign.discountCode = '';
        this.campaign.discountType = '';        
        this.campaign.discountAmount = '';
        this.campaign.isSiteWide = false;
        this.campaign.isActive = true;

      },
      loadCreateDiscount() {
        this.$refs['discount-modal'].show();
        this.$root.$emit("on-load-coun-search",'DISCOUNT');
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

      // handleSubmit() {
      //   this.$refs['campaign-modal'].hide()
      // },

      hideModal() {
        this.$refs['campaign-modal'].hide()
      },
      fromWhichProp(f) {
        this.fromWhich = f;
      },
      saveCampaign(){

        this.$v.$touch()
        if (this.$v.campaign.$invalid) 
        {
            this.submitStatus = 'ERROR'
            this.$toastr.error('Please fill up the form correctly.', 'Error', {timeOut: 1000});
        }
        else 
        {

          var that = this
          var formData = new FormData();
          formData.append('campaign', JSON.stringify(this.campaign)); 
          formData.append('campaignCategories', JSON.stringify(this.campaignCategories)); 
          formData.append('campaignProducts', JSON.stringify(this.campaignProducts)); 
          formData.append('campaignDiscounts', JSON.stringify(this.campaignDiscounts)); 
          formData.append('campaignCountries', JSON.stringify(this.selectedCountries)); 
          axios.post(process.env.API_URL+"api/addcampaign", formData).then(function(response){
            if (response.data.error){
              that.$toastr.error(response.data[1], 'Error', {timeOut: 1000});
            }else{
              that.$toastr.success(response.data[1], 'Success', {timeOut: 1000});              
              that.getCampaigns();
            }
            that.hideModal();
          });
        }  
      },
      getCampaigns(d = null) {
        this.filterType = 'All';
        this.viewType = 'rawdata';
        this.viewTitle = 'Campaign';
        var that = this;

        let loader = this.$loading.show({
          //container: this.fullPage ? null : this.$refs.formContainer,
          container: null,
          canCancel: true,
          onCancel: this.onCancel,
        });

        axios.all([
          axios({method: "GET", 
            "url": process.env.API_URL+"api/campaigns",
            params: d
            }).then(result => {
            this.campaignList = result.data.posts;
          }, error => {
             console.error(error);
          }),
          axios({ method: "GET", 
            "url": process.env.API_URL+"api/categoriesbyname" }).then(result => {
            this.campaignCatListByLevel = result.data.posts;
          }, error => {
             console.error(error);
          })
        ])
        .then(axios.spread(function (acct, perms) {
          loader.hide();
          let _showDetailsYN = null
          if(d)
            _showDetailsYN = true;
          else
            _showDetailsYN = false;
          that.$root.$emit("campaign-list",that.campaignList, that.campaignCatListByLevel, _showDetailsYN, that.filterType);
        }));
      },

      campaignPreview(d) {
        this.viewType = 'processeddata';
        this.viewTitle = 'Preview';
      },   

      removeItem(d, index){
        if(d=='Cat')
        {
          this.campaignCategories.splice(index, 1)
          this.campaignCategoriesTmp = this.campaignCategories;
        }
        else if(d=='Prod')
        {
          this.campaignProducts.splice(index, 1)
        }
        else if(d=='Disc')
        {
          this.campaignDiscounts.splice(index, 1)
        }        
      },
      onComplete: function(){
        this.saveCampaign();
      },
      processClicked(e)
      {
        this.$nextTick(function () {
          if(this.campaign.discountType=="$")  
          {
            this.selectedCountries = [];
            this.selectedCountries.push(event.target.value);
          }

        });
      },
      status(validation) {
        return {
          error: validation.$error,
          dirty: validation.$dirty
        }
      }, 
     gotToLocation(){
       this.$refs.wizard.changeTab(0,2)
     },
     gotBackCampaign(){
       this.$refs.wizard.changeTab(2,0)
     },
     setSkip()   
     {
        // if(event.target.value)
        //   this.skipToLocation = true;
        // else
        //   this.skipToLocation = false;
     }  
    }
  };
</script>
<style>
  table, th, td {
    border: 1px solid #dee2e6;
  }
  .table thead th {
    background-color: #f7f7f7;
    border-bottom: 0px solid #dee2e6;
  }
  .table th, .table td {
    padding: 0.25rem;
    vertical-align: middle;
  }
  .table {
    font-size: 13px;
    margin-bottom: 0rem; 
  }
/*  .table-active, .table-active>td, .table-active>th {
    background-color: rgb(220, 247, 255) !important;
  }  */
  .form-control {
    padding: .375rem .5em;
    font-size: .9rem;
  } 
  .custom-select {
    padding: .375rem .5em;
    font-size: .9rem;
  }
  .horiz-scroll {
    padding-bottom: 6px;
    overflow-x: auto;
  }
  @media (max-width: 1000px) {
    .horiz-scroll {
      overflow-x: auto;
    }
  }
  .horiz-scroll::-webkit-scrollbar {
    -webkit-appearance:none !important;
    height:8px !important;
    width:8px !important;
    background-color:#ddd !important
  }
  .horiz-scroll::-webkit-scrollbar-thumb {
    background-color:rgba(0, 0, 0, 0.24) !important
  }
  .vert-scroll {
    overflow-y: hidden;
  }
  .h-w {
    max-height: 300px;
  }
  .custom-box {
    margin-top: -5px !important;
  } 
  .btn-outline-active {
    color: #0060a75c;
    font-size: 14px;
    margin: 0px;
  }
  .btn-outline-active:hover {
      color: #004cff;
  }  
  .btn-outline-active-del {
    color: #0060a75c;
    font-size: 15px;
  }

  .btn-outline-active-del-1 {
    padding: .25rem .6rem;    
  }

  .btn-outline-active-del:hover {
    color: #ff0000;
  }    
  .btn-outline-active-del-icon {
    color: #0060a75c;
    font-size: 18px;
    margin-top: -2px;
  }
  .btn-outline-active-del-icon:hover {
    color: #ff0000;
  }    

  .badge {
    font-size: 90%;
    font-weight: 400;
    text-align: left;
    white-space: normal;
    padding-bottom: 6px;
    border-radius: 0rem;
  }


  .badge-light {
    color: #888888;
    background-color: #f1f4f7ad;
  }
  
  label {
    font-size: 14px;
    font-weight: 700;
  }

  dl, ol, ul {
    padding: 0px 0px 0px 0px !important;
  }

  .custom-control {
      min-height: 2rem !important; 
  }
  
  .modal-title {
    margin-bottom: 0;
    font-size: 1.1rem;
    font-weight: 500;
    color: #777777;
    /*text-shadow: 1px 1px 3px rgba(0,0,0,0.3);*/
  }  

  .btn {
    padding: .275rem .45rem;
  }

  .modal-header {
    background-color: #daf0ff;
    border-top-left-radius: 0rem;
    border-top-right-radius: 0rem;
    padding: .3rem .3rem .3rem .3rem !important;
    border-bottom: 0px solid #dee2e6;
  }
  .modal-footer {
    padding: 0rem .5rem .5rem 0rem !important; 
    border-top: 0px solid #dee2e6 !important; 
  }
  
  .modal-content {
    padding: 2px !important; 
  }
  
  .container-fluid {
    padding-right: 15px; 
    padding-left: 15px; 
  }

  .vue-form-wizard .wizard-header {
    padding: 1px 1px 10px 1px;
    margin-top: -10px;
  }

  .vue-form-wizard .wizard-nav-pills {
    margin-bottom: 15px;
  }

  .vue-form-wizard {
    padding-bottom: 0px;
  }

  .vue-form-wizard .wizard-tab-content {
    padding: 30px 0px 10px;
  }

  .vue-form-wizard .wizard-card-footer {
    padding: 0 0px;
    margin-bottom: -10px;
  }
  .dirty {
    border-color: #5A5;
    background: #fff;
  }

  .dirty:focus {
    outline-color: #8E8;
  }

  .error {
    border-color: red;
    background: #fff;
    color: red;
  }

  .error:focus {
    outline-color: #F99;
  }

  .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
    color: #007eca;
    font-weight: 600;
  }

  .card-header {
    padding: 0.3rem 1.25rem;
  }

  .card-header-tabs {
    margin-bottom: -.3rem;
  }

  .card-body {
    padding: .25rem;
  }

  .nav-link {
    padding: .3rem 1rem;
  }
  
  .modal-content {
    padding: 5px;
  }
  
  .modal-header .close {
    padding: 1rem 1.3rem;
  }
  .custom-select {
    padding: .2rem .3em;
    font-size: .7rem;
  }

  .page-link {
    line-height: .7;
  }

  .mb-1, .my-1 {
    margin-bottom: .1rem !important;
  }

  h4{
    font-size: 1rem !important;
    padding-left: 5px !important;
    font-weight: 600 !important;
  }
  /*  
  .fade {
    opacity: .5 !important;
  }*/
</style>
<style scoped>
  .container-fluid {
    padding-right: 0px; 
    padding-left: 0px; 
  }
</style>
