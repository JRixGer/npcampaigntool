<template>
  <div>
    <b-modal
      id="update-campaign-update-id"
      ref="campaign-update-modal"
      title="UPDATE CAMPAIGN"
      size="lg"
      no-close-on-backdrop
      no-fade
    >
       <form ref="form" @submit.stop.prevent="handleSubmit">
       <div>
          <form-wizard @on-complete="onComplete"
          ref="wizardUpdate"
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
                <div class="custom-box"><div v-for="(cat, index) in campaignCategories" style="width:auto; float:left"><b-badge variant="info" style="margin:3px"><span v-html="cat.completeName"></span> <font-awesome-icon icon="times" @click="removeItem('Cat',index)" class="btn-outline-active-del"/></b-badge></div></div>
                  <CategorySearchList></CategorySearchList>
                </b-form-group>
              </b-col>

              <b-col cols="12">
                <b-form-group
                  label="Products"
                  label-for="products-input"
                >
                <div class="custom-box"><span v-for="(prod, index) in campaignProducts"><b-badge variant="info" style="margin:3px">{{prod.prodName}} [{{prod.prodID}}] <font-awesome-icon icon="times" @click="removeItem('Prod',index)" class="btn-outline-active-del"/></b-badge></span></div>
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
                  <!-- <DiscountSearchList></DiscountSearchList> -->
                  </b-form-group>

                </b-col>
                <b-col cols="12">
                  <AddDiscount></AddDiscount>
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
<!--         <p class="float-left"></p>

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
          @click = "updateCampaign"
        >
          Save
        </b-button> -->
      </div>
    </b-modal>
  </div>
</template>


<script>
  import {showErrorMsg, showSuccessMsg, setDiscountsOption} from '../../helper';
  import axios from "axios";

  import _ from 'lodash';
  import { ModelSelect, MultiSelect } from 'vue-search-select';
  import { required, minLength, between, alphaNum, helpers} from 'vuelidate/lib/validators';
  const alpha = helpers.regex('alpha', /^[a-zA-Z]*$/);
  const number = helpers.regex('number', /^\d*$/);
  const phoneNumber = helpers.regex('phoneNumber', /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/);
  const alphaNumSpace = helpers.regex('alphaNumSpace', /^[A-Za-z,\d\s]+$/);
  const alphaSpace = helpers.regex('alphaSpace', /^[A-Za-z,\s]+$/);

  import ProductSearchList from './ProductSearchList.vue';
  import CategorySearchList from './CategorySearchList.vue';
  import DiscountSearchList from './DiscountSearchList.vue';
  import AddDiscount from './AddDiscount.vue';

  export default {
    components: {ModelSelect, MultiSelect, CategorySearchList, ProductSearchList, DiscountSearchList, AddDiscount},
    data() {
      return {
        campaign: [],
        discount: [],               
        productsByCatID: [],       
        campaignCategories: [], 
        campaignCategoriesTmp: [], 
        campaignProducts: [], 
        campaignProductsTmp: [],
        campaignDiscounts: [], 
        campaignDiscountsTmp: [],                 
        campaignCountries: [],
        campaignCountriesTmp: [],        
        discountsOptions:[],     
        discountTypeOptions:[
          { value: '$', text: 'Amount' },
          { value: '%', text: 'Percent' }
        ],
        selectedCountries: [],
        optionsCountries: [
          { text: 'Sweden', value: 'Sweden' },
          { text: 'Denmark', value: 'Denmark' },
          { text: 'Finland', value: 'Finland'},
          { text: 'Norway', value: 'Norway' }
        ],
        skipToLocation: false,
        fromWhere: "UPDATE",        
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
        "campaign.discountAmount":function(){
          if(this.campaign.discountType == "%" && this.campaign.discountAmount >= 50)
            this.$toastr.warning('Are you sure on this discount amount '+this.campaign.discountAmount+this.campaign.discountType+'?', 'Warning', {timeOut: 1500});
        }       
    }, 
    created() {

      this.$root.$on("campaign-update",(campaign, category, products, discounts, countries)=>{
        this.campaign = campaign;
        this.selectedCountries = [];

        if(this.campaign.isActive == 1)
          this.campaign.isActive = true
        else
          this.campaign.isActive = false

        if(this.campaign.isSiteWide == 1)
          this.campaign.isSiteWide = true
        else
          this.campaign.isSiteWide = false

        if(this.campaign.isAllowCombine == 1)
          this.campaign.isAllowCombine = true
        else
          this.campaign.isAllowCombine = false

        if(category)
          this.campaignCategories = category;

        if(products)
          this.campaignProducts = products;

        this.skipToLocation = false;
        if(discounts)
        {
          this.campaignDiscounts = discounts;
          if(discounts.length > 0) 
            this.skipToLocation = true;
        }

        if(countries)
        {
          this.campaignCountries = countries;
          for(let i = 0; i < this.campaignCountries.length; i++)
            this.selectedCountries.push(this.campaignCountries[i].name);
        }
        this.$refs['campaign-update-modal'].show();
        this.$refs.wizardUpdate.reset();
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
      //     this.campaignDiscounts.push({'camDiscID': e['camDiscID'], 'discountCode': e['discountCode'], 'description': e['description'],'numberOfUse': e['numberOfUse']});
      // }); 

      this.$root.$on("discount-options",(e)=>{
        this.discountsOptions = e; 
        this.discountsOptions.push({'value':'', 'text':''});  
      });  

      this.$root.$on("refresh-discount-edit",(e)=>{
        //console.log(this.campaignDiscounts); 
        this.campaignDiscountsTmp = this.campaignDiscounts.filter(c => c.discountCode === e['discountCode']); 
        if(this.campaignDiscountsTmp.length == 0)
          this.campaignDiscounts.push({'discountCode': e['discountCode'],'description': e['description'],'numberOfUse': e['numberOfUse']});

      });  

    }, 
    mounted() {
    },
    methods: {
      init(){
      },
      hideModal() {
        this.$refs['campaign-update-modal'].hide()
      },
      updateCampaign(){

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

          let exist = null;  
          this.campaignCountries = this.campaignCountries.filter(c => (jQuery.inArray(c.name, this.selectedCountries) > -1)); 
          for (let i = 0; i < this.selectedCountries.length; i++)
          {
            if(this.campaignCountries.length > 0)
            {
              exist = this.campaignCountries.filter(c => c.name === this.selectedCountries[i]); 
              if (!exist.length>0) 
                this.campaignCountries.push({'name':this.selectedCountries[i]});
            }
            else
              this.campaignCountries.push({'name':this.selectedCountries[i]});
          }          

          formData.append('campaignCountries', JSON.stringify(this.campaignCountries)); 
          formData.append('campaignDiscounts', JSON.stringify(this.campaignDiscounts)); 

          axios.post(process.env.API_URL+"api/updatecampaign", formData).then(function(response){
            if (response.data.error){
              that.$toastr.error(response.data[1], 'Error', {timeOut: 1000});
            }else{
              
              that.$root.$emit("campaign-updated",true);
              that.$toastr.success(response.data[1], 'Success', {timeOut: 1000});
            }
            that.hideModal();
          });
        }
      },
      removeItem(d, index){
        if(d=='Cat')
        {
          this.campaignCategories.splice(index, 1)
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
        this.updateCampaign();
      },
      saveDiscount(){
        var that = this
        var formData = new FormData();
        formData.append('discount', JSON.stringify(this.discount)); 
        //formData.append('discountCountries', JSON.stringify(this.discountCountries)); 
        //formData.append('action', 'CREATE DISCOUNT'); 
        axios.post(process.env.API_URL+"api/adddiscount", formData).then(function(response){
          if (response.data.error){
            that.$toastr.error(response.data[1], 'Error', {timeOut: 1000});
          }else{
            that.$toastr.success(response.data[1], 'Success', {timeOut: 1000});
          }
        });
      },  
      processClicked(e)
      {
        let that = this;
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
       this.$refs.wizardUpdate.changeTab(0,2)
     },
     gotBackCampaign(){
       this.$refs.wizardUpdate.changeTab(2,0)
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
</style>