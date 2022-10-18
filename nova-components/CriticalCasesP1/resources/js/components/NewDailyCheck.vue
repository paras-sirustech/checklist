<template>
  <div>
    <loading-view v-if="loading" :loading="loading">
      <loading-card :loading="loading" class="card"></loading-card>
    </loading-view>

    <form @submit.prevent="startCriticalCases" autocomplete="off" v-if="!loading && formActive">
      <div class="mb-8">
        <h1 class="mb-3 text-90 font-normal text-2xl">{{ this.selectedShop.name }} - {{ this.date | moment("MMM Do, YYYY") }}</h1>
        <div class="card">
          <div v-for="item in selectedShop.checklist.items" :key="item.id">
            <checklist-input :date="date" :item="item" :shop="selectedShop" @selectItemOption="setItemOption"></checklist-input>
          </div>
        </div>
      </div>
      <div class="flex items-center">
        <a tabindex="0" class="btn btn-link dim cursor-pointer text-80 ml-auto mr-6" onClick="javascript:history.go(-1)">Cancel</a>
        <button type="button" :disabled="!checklistSubmitReady" @click="fileDailyCheck()" class="btn btn-default btn-primary inline-flex items-center relative" dusk="next-button">
          <span class>File Daily Check</span>
          <!---->
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import ChecklistInput from "../components/ChecklistInput.vue";

import { Errors } from "laravel-nova";
export default {
  components: {
    "checklist-input": ChecklistInput
  },

  props: ["shop", "date"],
  data: function() {
    return {
      loading: false,
      formActive: false,
      modalOpen: false,
      selectedShop: "",
      checklistSubmitReady: false,
      checklistItemStatus: [],
      checklistOptions: [
        { label: "Okay", value: "Okay" },
        { label: "Not Okay", value: "Not Okay" }
      ],
      validationErrors: new Errors()
    };
  },
  mounted() {
    this.startCriticalCases();
  },
  methods: {
    fileDailyCheck: function() {
      this.loading = true;
      axios
        .post("/nova-vendor/critical-cases-p1/file", {
          shop: this.shop,
          date: this.date
        })
        .then(
          function(response) {
            this.loading = false;

            this.$toasted.show(response.data.message, {
              type: "success"
            });

            setTimeout(function() {
              window.location.href = response.data.redirect;
            }, 1000);
          }.bind(this)
        )
        .catch(
          function(error) {
            console.log(error);
            this.loading = false;
            if (error.response.data.message) {
              this.$toasted.show(error.response.data.message, {
                type: "error"
              });
            } else {
              this.$toasted.show(
                "Error! Unable to File. Please try again later or contact support",
                { type: "error" }
              );
            }
          }.bind(this)
        );
    },
    setItemOption: function(value) {
      var index = this.checklistItemStatus
        .map(x => {
          return x.id;
        })
        .indexOf(value.id);

      if (index >= 0) {
        this.checklistItemStatus.splice(index, 1);
      }

      this.checklistItemStatus.push(value);

      if (
        this.checklistItemStatus.length ==
        this.selectedShop.checklist.items.length
      ) {
        this.checklistSubmitReady = true;
      }
    },
    startCriticalCases: function() {
      axios
        .post("/nova-vendor/critical-cases-p1/start", {
          shop: this.shop,
          date: this.date
        })
        .then(
          function(response) {
            if (response.data.redirect) {
              if (response.data.message) {
                this.$toasted.show(response.data.message, { type: "info" });
              }

              setTimeout(function() {
                window.location.href = response.data.redirect;
              }, 3000);
            } else {
              this.formActive = true;
              this.selectedShop = response.data.shop;
            }
          }.bind(this)
        )
        .catch(
          function(error) {
            console.log(error);
            if (error.response.data.message) {
              this.$toasted.show(error.response.data.message, {
                type: "error"
              });
            } else {
              this.$toasted.show(
                "Error! Unable to start. Please try again later or contact support",
                { type: "error" }
              );
            }
          }.bind(this)
        );
    }
  },
  filters: {
    moment: function(date, format) {
      if (!format) format = "MMMM Do YYYY, h:mm:ss a";
      return moment(date).format(format);
    }
  }
};
</script>

<style>
/* Scoped Styles */
</style>
