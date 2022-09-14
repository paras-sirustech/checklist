<template>
  <div>
    <loading-view v-if="loading" :loading="loading">
      <loading-card :loading="loading" class="card"></loading-card>
    </loading-view>
    <div v-else>
      <div class="flex border-b border-40">
        <div class="w-1/5 px-8 py-6">
          <label for="name" class="inline-block text-80 pt-2 leading-tight">
            {{ item.name }}
            <span class="text-danger text-sm">*</span>
          </label>
        </div>

        <div class="py-6 px-8 w-1/2">
          <select v-model="dailyCheckItemStatus" class="w-full form-control form-select" @change="setOption">
            <option value selected="selected" disabled="disabled">Choose an option</option>
            <option value="Okay">Okay</option>
            <option value="Not Okay">Not Okay</option>
          </select>
          <!---->
          <div class="help-text help-text mt-2" v-if="createdSupportTicket">
            Support Ticket:
            <a target="_blank" :href="createdSupportTicket.url">ID: {{ createdSupportTicket.id }} - {{ createdSupportTicket.subject }}</a>
          </div>
        </div>
      </div>
      <div v-if="showCreateIssueForm" class="bg-30">
        <div class="flex border-b border-40">
          <div class="w-1/5 px-8 py-6">
            <label for="description" class="inline-block text-80 pt-2 leading-tight">
              Describe the Issue in {{ item.name }}
              <!---->
            </label>
          </div>
          <div class="py-6 px-8 w-4/5">
            <textarea v-model="issueDescription" rows="5" placeholder="Description" class="w-full form-control form-input form-input-bordered py-3 h-auto"></textarea>
            <!---->
            <div class="help-text help-text mt-2"></div>
          </div>
        </div>
        <div class="flex border-b border-40">
          <div class="w-1/5 px-8 py-6">
            <label for="attachment" class="inline-block text-80 pt-2 leading-tight">
              Attachment
              <!---->
            </label>
          </div>
          <div class="py-6 px-8 w-1/2">
            <span class="form-file mr-4">
              <input type="file" ref="file" v-on:change="handleFileUpload()" />
            </span>
          </div>
        </div>
        <div class="flex border-b border-40">
          <div class="w-1/5 px-8 py-6">
            <!---->
          </div>
          <div class="py-6 px-8 w-4/5">
            <button type="button" :disabled="!this.issueDescription" class="btn btn-default btn-primary inline-flex items-center relative" @click="createDailyCheckIssue">
              <span class>Save and Create Ticket</span>
              <!---->
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: ["shop", "item", "date"],
  data() {
    return {
      loading: false,
      issueDescription: "",
      dailyCheckItemStatus: "",
      showCreateIssueForm: false,
      createdSupportTicket: "",
      file: ""
    };
  },
  methods: {
    notifyParent() {
      this.$emit("selectItemOption", {
        id: this.item.id,
        value: this.dailyCheckItemStatus
      });
    },
    setOption(event) {
      if (this.dailyCheckItemStatus == "Okay") {
        this.showCreateIssueForm = false;
        this.submitDailyCheckItem();
      }

      if (this.dailyCheckItemStatus == "Not Okay") {
        this.showCreateIssueForm = true;
      }
    },
    createDailyCheckIssue: function() {
      this.loading = true;

      var formData = new FormData();
      formData.append("checklist_item_id", this.item.id);
      formData.append("checklist_item_name", this.item.name);
      formData.append("shop", this.shop.id);
      formData.append("date", this.date);
      formData.append("status", this.dailyCheckItemStatus);
      formData.append("description", this.issueDescription);

      if (this.file && this.file.name != "") {
        formData.append("file", this.file);
      }

      axios
        .post("/nova-vendor/daily-checks/item-create-issue", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            "Cache-Control": "no-cache"
          }
        })
        .then(
          function(response) {
            this.loading = false;
            this.notifyParent();
            this.showCreateIssueForm = false;
            this.createdSupportTicket = response.data.support_ticket;
            this.$toasted.show(response.data.message, { type: "success" });
          }.bind(this)
        )
        .catch(
          function(error) {
            console.log(error);
            this.loading = false;
            if (error.response.status === 413) {
              this.$toasted.show(
                "Error! Attached file is too large. Please try with a smaller attachment",
                { type: "error" }
              );
            } else {
              if (error.response.data.message) {
                this.$toasted.show(error.response.data.message, {
                  type: "error"
                });
              } else {
                this.$toasted.show(
                  "Error! Unable to create Issue. Please try again later or contact support",
                  { type: "error" }
                );
              }
            }
          }.bind(this)
        );
    },
    submitDailyCheckItem: function() {
      this.loading = true;
      axios
        .post("/nova-vendor/daily-checks/save-item", {
          checklist_item_id: this.item.id,
          checklist_item_name: this.item.name,
          shop: this.shop.id,
          date: this.date,
          status: this.dailyCheckItemStatus
        })
        .then(
          function(response) {
            this.loading = false;
            this.notifyParent();
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
                "Error! Unable to save item. Please try again later or contact support",
                { type: "error" }
              );
            }
          }.bind(this)
        );
    },
    handleFileUpload() {
      if (this.$refs.file.files && this.$refs.file.files.length > 0) {
        this.file = this.$refs.file.files[0];
      } else {
        this.file = "";
      }

      console.log(this.file);
    }
  }
};
</script>
<style>
/* Scoped Styles */
</style>