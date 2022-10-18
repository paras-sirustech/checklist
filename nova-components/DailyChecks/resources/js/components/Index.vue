<template>
  <div>
    <loading-view v-if="loading" :loading="loading">
        <loading-card :loading="loading" class="card"></loading-card>
    </loading-view>

  <form @submit.prevent="startDailyCheck" autocomplete="off" v-if="!loading">

      <div class="mb-8">
        <h1 class="mb-3 text-90 font-normal text-2xl">Daily Check</h1>
        <div class="card">
          <form-select-field
                :field="{
                    attribute: 'shop',
                    required: true,
                    options: this.shops,
                    value: this.selectedShop.value,
                    name: __('Shop')
                }"
                :errors="validationErrors"
            />

            <form-text-field
                :field="{
                    attribute: 'date',
                    required: true,
                    readonly: true,
                    value: this.todaysDate,
                    type: 'date',
                    name: __('Date'),
                    placeholder: __('Select Date')
                }"
                :errors="validationErrors"
            />


        </div>
      </div>
      <div class="flex items-center">
        <a tabindex="0" class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6">Cancel</a>
        <button type="button" @click="startDailyCheck()" class="btn btn-default btn-primary inline-flex items-center relative" dusk="next-button">
          <span class>Next</span>
          <!---->
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { Errors } from 'laravel-nova';
export default {
  data: function() {
    return {
      loading: false,
      shops: [],
      checkingDate: '',
      selectedShop: '',
      validationErrors: new Errors(),
    };
  },
  mounted() {
    this.getShops();
  },
  methods: {
    getShops: function() {
      this.loading = true;
      axios
        .get("/nova-vendor/daily-checks/shops")
        .then(
          function(response) {
            this.loading = false;
            this.shops = response.data;
            if(this.shops.length>0){
              this.selectedShop = this.shops[0];
            }
          }.bind(this)
        )
        .catch(
          function(error) {
            this.loading = false;
          }.bind(this)
        );
    },
    greet: function() {
      console.log('Greet');
    },
    startDailyCheck: function() {
      axios
        .post("/nova-vendor/daily-checks/start", {
          shop: document.getElementById('shop').value,
          date: document.getElementById('date').value
        })
        .then(
          function(response) {
                this.$router.push({
                    path: '/daily-checks/shops/' + document.getElementById('shop').value +'/' + document.getElementById('date').value  + '/new',
                })
          }.bind(this)
        )
        .catch(
          function(error) {
            if(error.response.data.message){
              this.$toasted.show(error.response.data.message, { type: 'error' });
            }

            if (error.response.status == 422) {
                this.validationErrors = new Errors(error.response.data.errors)
            }
          }.bind(this)
        );
    }
  },
  computed: {
    todaysDate: function () {
      return moment().format('YYYY-MM-DD');
    }
  }
};
</script>

<style>
/* Scoped Styles */
</style>
