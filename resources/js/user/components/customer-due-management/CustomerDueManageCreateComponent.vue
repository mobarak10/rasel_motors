<template>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Manage Due </h5>

            <div class="btn-group" role="group" aria-level="Action area">
                <a :href="listURL" title="show due manage list" class="btn btn-success" style="margin-right: 5px">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </a>
            </div>
        </div>

        <div class="card-body">
            <form method="post" @submit.prevent="save" class="row">
                <!-- start get customer -->
                <div class="form-group col-md-12 required">
                    <label for="customers">Customer</label>
                    <select id="customers" class="form-control" @change="getCustomerBalance" v-model="customerId" required>
                        <option selected disabled :value="null">Choose one</option>
                        <option v-for="(customer, index) in customers" :value="customer.id" :key="index">{{ customer.name }}</option>
                    </select>
                </div>
                <!-- end get customer -->

                 <!-- balance for customer start -->
                <div class="col-md-12" v-if="customerBalance">
                    <p class="d-block bg-dark text-light p-1 px-2">
                        BDT {{ Math.abs(customerBalance) }} {{ customerBalance > 0 ? 'Receivable' : 'Payable' }}
                    </p>
                </div>
                <!-- balance for customer end -->

                <div class="form-group col-md-6 required">
                    <label for="date">Date</label>
                    <input type="date" v-model="date" class="form-control" id="date" required>
                </div>

                <div class="form-group col-md-6 required">
                    <label for="payment">Pay / Receive Amount</label>
                    <div class="input-group">
                        <input type="number" v-model="amount" class="form-control" id="payment" placeholder="Enter Amount (BDT)" required>
                        <div class="input-group-append">
                            <select v-model="paymentType" class="btn btn-primary dropdown-toggle">
                                <option class="dropdown-item" value="paid">Pay</option>
                                <option class="dropdown-item" value="received">Receive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- select cash/bank start -->
                <div class="col-md-12">
                    <div class="form-check form-check-inline">
                        <input type="radio" style="cursor: pointer;" v-model="where" class="form-check-input" name="rom" id="cash" value="cash">
                        <label for="cash" class="form-check-label">Cash</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input type="radio" style="cursor: pointer;" v-model="where" class="form-check-input" name="rom" id="bank" value="bank">
                        <label for="bank" class="form-check-label">Bank</label>
                    </div>
                </div>
                <!-- select cash/bank end -->

                <!-- start cash part -->
                <div v-if="where === 'cash'" class="col-md-12 pt-3">
                    <div class="form-group required">
                        <label for="cash">Cash</label>
                        <select class="form-control" v-model="cashId" @change="getCashBalance" required>
                            <option selected disabled :value="null">Choose one</option>
                            <option v-for="(cash, index) in cashes" :value="cash.id" :key="index">{{ cash.title }}</option>
                        </select>
                    </div>
                </div>
                <!-- end cash part -->

                <!-- start bank part -->
                <div v-if="where === 'bank'" class="col-md-12 pt-3">
                    <div class="row">
                        <div class="form-group col-md-6 required">
                            <label for="bank">Bank</label>
                            <select class="form-control" v-model="bankId" @change="getBankAccount(bankId)" required>
                                <option selected disabled :value="null">Choose one</option>
                                <option v-for="(bank, index) in banks" :value="bank.id" :key="index">{{ bank.name }}</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6 required">
                            <label for="bank-account">Bank Account</label>
                            <select id="bank-account" v-model="bankAccountId" @change="getAccountBalance" class="form-control" required>
                                <option :value="null" selected disabled>Choose one</option>
                                <option v-for="(account, index) in accounts" :value="account.id" :key="index">{{ account.account_name }}</option>
                            </select>
                        </div>

                        <!-- balance for bank start -->
                        <div class="col-md-12" v-if="bankAccountBalance">
                            <p class="d-block bg-dark text-light p-1 px-2">BDT {{ bankAccountBalance }} </p>
                        </div>
                        <!-- balance for bank end -->

                        <div class="form-group col-md-6">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" id="date">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="check">Check number</label>
                            <input type="text" class="form-control" id="check">
                        </div>
                    </div>
                </div>
                <!-- end bank part -->

                <!-- balance for cash start -->
                <div class="col-md-12" v-if="cashBalance">
                    <p class="d-block bg-dark text-light p-1 px-2">BDT {{ cashBalance }} </p>
                </div>
                <!-- balance for cash end -->
                <div class="form-group col-md-12">
                    <label for="description">Description</label>
                    <textarea class="form-control" v-model="description" id="description" placeholder="Write something (optional)"></textarea>
                </div>

                <div class="form-group col-md-12 text-right">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'DueManagementComponent',
        props: ['customers', 'banks', 'cashes', 'brands'],
        computed: {
            listURL(){
                return baseURL + 'user/customerDueManage'
            },
        },
        data() {
            return {
                accounts: [],
                customerId: null,
                customerBalance: null,
                brandId: null,
                date: new Date().toISOString().substr(0, 10),
                amount: null,
                paymentType: 'received',
                where: 'cash',
                cashId: null,
                bankId: null,
                bankAccountId: null,
                bankAccountBalance: null,
                cashBalance: null,
                description: null,
            }
        },

        methods: {
            getCustomerBalance() {
                if(this.customerId){
                    this.customerBalance = this.customers.find(customer => customer.id === this.customerId).balance
                }
            },

            getCashBalance(){
                this.cashBalance = this.cashes.find(cash => cash.id === this.cashId).amount;
                this.bankId = null;
                this.bankAccountId = null;
                this.bankAccountBalance = null;
            },

            getBankAccount(bank_id) {
                this.accounts = this.banks.find(bank => bank.id === bank_id).bank_accounts
                this.cashId = null;
                this.cashBalance = null;
            },

            getAccountBalance() {
                this.bankAccountBalance = this.accounts.find(account => account.id === this.bankAccountId).balance
                this.cashId = null;
                this.cashBalance = null;
            },

            save() {
                this.$awn.asyncBlock(
					axios.post(baseURL + 'user/customerDueManage', {
                        'customer_id': this.customerId,
                        'brand_id': this.brandId,
                        "date": this.date,
                        "amount": this.amount,
                        "payment_type": this.paymentType,
                        "paid_from": this.where,
                        "cash_id": this.cashId,
                        "bank_account_id": this.bankAccountId,
                        "description": this.description,
                    }),
					response => {
                        // console
                        console.log(response.data);
                        this.accounts = []
                        this.customerId = null
                        this.customerBalance = null
                        this.brandId = null
                        this.date = new Date().toISOString().substr(0, 10)
                        this.amount = null
                        this.paymentType = 'received'
                        this.where = 'cash'
                        this.cashId = null
                        this.bankId = null
                        this.bankAccountId = null
                        this.bankAccountBalance = null
                        this.cashBalance = null
                        this.description = null
                        // flase message
						this.$awn.success("Due manage successfully.");
					},
					error => {
						if (error.response.status === 422) { // validation error
							this.errors = error.response.data.errors
							this.$awn.alert('Opps! Enter the valid information of product')
						} else {
							this.$awn.alert('Opps! something went wrong. Try again later')
						}
					}
				)
            }

        },

        mounted() {

        }
    }
</script>
