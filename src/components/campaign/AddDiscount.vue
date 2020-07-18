<template>
  <div style="padding-top:20px">
    <b-row>
      <b-col cols="12">
      <h5 style="margin-top:20px">Create New Code</h5>
      </b-col>
      <b-col cols="6">
        <b-form-group
          label="Discount Code"
          label-for="discount-code-input"
          invalid-feedback="Discount code is required"
        >
          <b-form-input
            id="discount-code-input"
            v-model="$v.discount.discountCode.$model" :class="status($v.discount.discountCode)"
            required
            @input="checkCouponCode()" 
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
            v-model="$v.discount.description.$model" :class="status($v.discount.description)"
            required
          ></b-form-input>
        </b-form-group>
      </b-col>

    </b-row>
   <b-row>
      
      <b-col cols="6">
        <b-form-group
          label="Number of Use"
          label-for="no-of-use-input"
          invalid-feedback="Number of use is required"
        >
          <b-form-input
            id="max-qty-input"
            v-model="$v.discount.numberOfUse.$model" :class="status($v.discount.numberOfUse)"
            required
          ></b-form-input>
        </b-form-group>
      </b-col>

      <b-col cols="6" style="margin-bottom:50px; margin-top: 30px;">
        <!-- <b-button
          type="button"
          variant="secondary" 
          size="sm"
          class="float-left"
          @click = "saveDiscount"
        >
          <span class="ti ti-plus"></span> Add Code
        </b-button> -->

          <b-button-group>
            <b-button variant="secondary" class="btn-outline" @click="saveDiscount" :disabled="duplicateCode"><font-awesome-icon icon="plus"/> Add code</b-button>
            <b-button variant="secondary" class="btn-outline" @click="generate" >Generate code</b-button>
          </b-button-group>


      </b-col>
    </b-row>
   
  </div>
</template>

<script>
  import {showErrorMsg, showSuccessMsg} from '../../helper';
  import axios from "axios";
  import _ from 'lodash';
  import { required, minLength, between, alphaNum, helpers} from 'vuelidate/lib/validators';
  const alpha = helpers.regex('alpha', /^[a-zA-Z]*$/);
  const number = helpers.regex('number', /^\d*$/);
  const phoneNumber = helpers.regex('phoneNumber', /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/);
  const alphaNumSpace = helpers.regex('alphaNumSpace', /^[A-Za-z,\d\s]+$/);
  const alphaSpace = helpers.regex('alphaSpace', /^[A-Za-z,\s]+$/);

  export default {
    props:["fromWhere"],
    data() {
      return {
        discount: {
          disID: '',
          description: '',
          discountCode: '',
          numberOfUse:'',
          isActive: '',
          draw: 0,
          search: '',
        },
        discountCountries: [],
        discountCountriesTmp: [],
        duplicateCode: false        

      }
    },
    validations: {
      discount: {
        description: {
              required,
              minLength: minLength(2)
          },
        discountCode: {
              required,
              minLength: minLength(2)
          },
        numberOfUse: {
              number
          }
      }   
    },    
    computed: {
    },
    watch : {
      "duplicateCode":function(){
        if(this.duplicateCode)
          this.$toastr.error('Duplicate coupon code entered', 'Error', {timeOut: 1000});

      },
    }, 
    created() {

    }, 
    mounted() {
    },
    methods: {
      checkCouponCode(url = process.env.API_URL+"api/couponbyname") {
        this.discount.draw++;
        axios.get(url, {params: this.discount})
          .then(response => {
              let data = response.data;
              if (this.discount.draw == data.draw) {
                  this.duplicateCode = data.posts;
                  //console.log('>>>> '+this.duplicateCode);
              }

          })
          .catch(errors => {
            this.duplicateCode = false;
            console.log(errors);
          });

      },
      saveDiscount(){
        this.$v.$touch()
        if (this.$v.discount.$invalid || this.$v.discount.$invalid) 
        {
            this.submitStatus = 'ERROR'
            this.$toastr.error('Please fill up the form correctly.', 'Error', {timeOut: 1000});
        }
        else 
        {        

          if(this.fromWhere == "ADD")
            this.$root.$emit("refresh-discount-add",this.discount);
          else
            this.$root.$emit("refresh-discount-edit",this.discount);


          this.discount.description = '';
          this.discount.discountCode = '';
          this.discount.numberOfUse = '';

        }  
      },
      status(validation) {
        return {
          error: validation.$error,
          dirty: validation.$dirty
        }
      },
      generate () {
        let CharacterSet = '';
        let code = '';
        
        CharacterSet += 'abcdefghijklmnopqrstuvwxyz';
        CharacterSet += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        CharacterSet += '0123456789';
      
        for(let i=0; i < 8; i++) {
          code += CharacterSet.charAt(Math.floor(Math.random() * CharacterSet.length));
        }
        this.discount.discountCode = code;

      }
    }
  };
</script>
<style>

</style>