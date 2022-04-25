<template>
    <div class="pos">
        <div class="row">
            <div class="col-12">
                <div class="container-fluid">
                    <form action="" @submit.prevent="purchase">
                        <!-- Top Form Start-->
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <input
                                    type="date"
                                    class="form-control form-control-sm"
                                    v-model="form.date"
                                />
                            </div>

                            <div class="col-md-3">
                                <v-select
                                    :options="parties"
                                    v-model="partyId"
                                    :reduce="party => party.id"
                                    placeholder="Select Client"
                                    @input="getPartyDetails(partyId)"
                                    label="name">
                                </v-select>
                            </div>

                            <div class="col-md-3">
                                <select
                                    :disabled="disableWarehouse"
                                    class="form-control form-control-sm"
                                    v-model="warehouse_id">
                                    <option :value="null" disabled>Select warehouse</option>
                                    <option
                                        v-for="(warehouse,
                                        warehouseIndex) in warehouses"
                                        :value="warehouse.id"
                                        :key="warehouseIndex"
                                        v-text="warehouse.title"
                                    />
                                </select>
                            </div>

                            <div class="col-md-3">
                                <v-select
                                    :options="products"
                                    label="name"
                                    v-model="selectedProduct"
                                    placeholder="Select product"
                                    @input="onProductSelected"
                                    class="bg-white">
                                </v-select>
                            </div>
                        </div>
                        <!-- Top Form End -->

                        <!-- Table Form Start -->
                        <div class="row">
                            <div class="my-2 col-12 border-top border-bottom">
                                <table class="table my-2 table-striped table-bordered table-sm">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th style="min-width: 180px">Product Name</th>
                                        <th class="text-right">Stock</th>
                                        <th>Quantity</th>
                                        <th>Purchase Price</th>
                                        <th class="text-right">Total</th>
                                        <th class="text-center print-none">
                                            Action
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-if="selectedProducts.length === 0">
                                        <td colspan="20" class="text-center">
                                            No product selected
                                        </td>
                                    </tr>

                                    <tr
                                        v-for="(product,
                                            productIndex) in selectedProducts"
                                        :key="productIndex"
                                    >
                                        <td>{{ productIndex + 1 }}.</td>
                                        <td>{{ product.name }}</td>

                                        <td class="text-right">
                                                <span :class="product.total_product_quantity >= 0 ? 'text-success' : 'text-danger'">
                                                    {{ product.display_quantity }}
                                                </span>
                                        </td>
                                        <td>
                                            <!-- Quantity -->
                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    aria-describedby="quantityError"
                                                    v-for="(label,
                                                        labelIndex) in product
                                                        .product_unit_labels"
                                                    :placeholder="label"
                                                    :key="labelIndex"
                                                    :value="product.quantity_in_unit[labelIndex]"
                                                    @blur="
                                                        addQuantity(
                                                                $event,
                                                                product.id,
                                                                labelIndex
                                                            )"
                                                    @change="
                                                            addQuantity(
                                                                $event,
                                                                product.id,
                                                                labelIndex
                                                            )"
                                                    @keyup="
                                                            addQuantity(
                                                                $event,
                                                                product.id,
                                                                labelIndex
                                                            )"
                                                    min="0"
                                                    class="form-control form-control-sm"
                                                />
                                            </div>
                                            <div v-if="product.error" id="quantityError" class="form-text text-danger">
                                                {{ product.error }}
                                            </div>
                                        </td>
                                        <td>
                                            <!-- Purchase Price -->
                                            <input
                                                type="number"
                                                step="any"
                                                class="form-control form-control-sm"
                                                v-model.trim="product.purchase_price"
                                            />
                                        </td>

                                        <td class="text-right">
                                            <div>
                                                {{
                                                    Number.parseFloat(
                                                        (product.total_price =
                                                            parseFloat(product.purchase_quantity || 0)
                                                            * parseFloat(product.purchase_price || 0))).toFixed(2)
                                                }}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            <button
                                                class="btn btn-sm btn-danger"
                                                type="button"
                                                @click.prevent="selectedProducts.splice(productIndex,1)"
                                            >
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Table Form End -->

                        <!-- Bottom Section Start -->
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <!-- Bottom Left Section Start -->
                                    <div class="col-md-6">
                                        <!-- Client Select Start -->
                                        <!-- Remark Start -->
                                        <div class="form-group row">
                                            <label class="col-md-3 col-form-label" for="note">
                                                Note
                                            </label>

                                            <div class="col-md-9">
                                                <textarea
                                                    class="form-control form-control-sm"
                                                    id="note"
                                                    v-model="form.note"
                                                    rows="8"
                                                    cols="8">
                                                </textarea>
                                            </div>
                                        </div>
                                        <!-- Remark End -->
                                    </div>
                                    <!-- Bottom Left Section End -->

                                    <!-- Bottom Right Section Start -->
                                    <div class="col-md-6">
                                        <!-- Total Amount Start -->
                                        <div class="form-group row">
                                            <label class="text-right col-3 col-form-label" for="total-amount">
                                                Total Amount
                                            </label>

                                            <div class="col-9">
                                                <div class="input-group input-group-sm">
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-sm"
                                                        id="total-amount"
                                                        autocomplete="off"
                                                        :value="Number.parseFloat(subtotal).toFixed(2)"
                                                        disabled
                                                    />

                                                    <div class="input-group-append">
                                                        <span class="input-group-text">৳</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Total Amount End -->

                                        <!-- previous balance start -->
                                        <div class="form-group row">
                                            <label for="previous_balance" class="text-right col-3 col-form-label">Previous Balance</label>
                                            <div class="col-5">
                                                <input type="number" disabled class="form-control form-control-sm" :value="Math.abs(partyBalance)" id="previous_balance">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" disabled :value="(partyBalance >= 0) ? 'Receivable' : 'Payable'" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <!-- previous balance start -->

                                        <!-- Total Discount Start -->
                                        <div class="form-group row">
                                            <div class="text-right col-5 col-form-label">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" v-model="form.payment.discountType" id="flat" value="flat">
                                                    <label class="form-check-label" for="flat">Flat Discount</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" v-model="form.payment.discountType" id="percentage" value="percentage">
                                                    <label class="form-check-label" for="percentage">Discount (%)</label>
                                                </div>
                                            </div>

                                            <div class="col-7">
                                                <div class="input-group input-group-sm">
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-sm"
                                                        id="total-discount"
                                                        v-model="form.payment.discount"
                                                        autocomplete="off"
                                                    />

                                                    <div class="input-group-append">
                                                        <span class="input-group-text">৳</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Total Discount End -->

                                        <!-- labour cost Start -->
                                        <div class="form-group row">
                                            <div class="text-right col-3 col-form-label">
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label" for="labour-cost">Labour Cost</label>
                                                </div>
                                            </div>

                                            <div class="col-9">
                                                <div class="input-group input-group-sm">
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-sm"
                                                        id="labour-cost"
                                                        step="any"
                                                        v-model="form.labourCost"
                                                    />

                                                    <div class="input-group-append">
                                                        <span class="input-group-text">৳</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- labour cost End -->

                                        <!-- transport cost Start -->
                                        <div class="form-group row">
                                            <div class="text-right col-3 col-form-label">
                                                <div class="form-check form-check-inline">
                                                    <label class="form-check-label" for="transport-cost">Transport Cost</label>
                                                </div>
                                            </div>

                                            <div class="col-9">
                                                <div class="input-group input-group-sm">
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-sm"
                                                        id="transport-cost"
                                                        step="any"
                                                        v-model="form.transportCost"
                                                    />

                                                    <div class="input-group-append">
                                                        <span class="input-group-text">৳</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- transport cost End -->

                                        <!-- Grand Total Start -->
                                        <div class="form-group row">
                                            <label
                                                class="text-right col-3 col-form-label"
                                                for="grand-total">
                                                Grand Total
                                            </label>

                                            <div class="col-9">
                                                <div class="input-group input-group-sm">
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-sm"
                                                        id="grand-total"
                                                        autocomplete="off"
                                                        disabled
                                                        :value="
                                                            Number.parseFloat(
                                                                ((parseFloat(subtotal) + parseFloat(form.labourCost || 0) + parseFloat(form.transportCost || 0))
                                                                - parseFloat(partyBalance || 0))
                                                                -
                                                                parseFloat(totalDiscount || 0)).toFixed(2)"
                                                    />

                                                    <div class="input-group-append">
                                                        <span class="input-group-text">৳</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Grand Total End -->

                                        <!-- Paid Start -->
                                        <div class="form-group row required">
                                            <label
                                                class="text-right col-3 col-form-label"
                                                for="paid">
                                                Paid
                                            </label>
                                            <div class="col-6">
                                                <div class="input-group input-group-sm">
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-sm"
                                                        id="paid"
                                                        min="0"
                                                        required
                                                        autocomplete="off"
                                                        v-model="form.payment.paid"/>

                                                    <div class="input-group-append">
                                                        <span class="input-group-text">৳</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="input-group-append">
                                                    <select
                                                        class="form-control form-control-sm"
                                                        v-model="form.payment.method">
                                                        <option value="cash">Cash</option>
                                                        <option value="bank">Bank</option>
                                                        <option value="bkash">Bkash</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Paid End -->

                                        <!-- Cash Extra Field Start -->
                                        <div v-if="form.payment.method == 'cash'">
                                            <div class="form-group row">
                                                <label
                                                    class="text-right col-3 col-form-label"
                                                    for="cash-name">
                                                    Cash Name
                                                </label>
                                                <div class="col-9">
                                                    <select
                                                        id="cash-name"
                                                        class="form-control form-control-sm"
                                                        v-model="paymentInfo.cash_id">
                                                        <option
                                                            v-for="(cash,
                                                            cashIndex) in cashes"
                                                            :key="cashIndex"
                                                            :value="cash.id">
                                                            {{ cash.title }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Cash Extra Field End -->

                                        <!-- Bank Extra Field Start -->
                                        <div v-if="form.payment.method == 'bank'">
                                            <div class="form-group row">
                                                <label
                                                    class="text-right col-3 col-form-label"
                                                    for="bank-name">
                                                    Bank Name
                                                </label>

                                                <div class="col-9">
                                                    <select
                                                        id="bank-name"
                                                        class="form-control form-control-sm"
                                                        v-model="paymentInfo.bank_account_id">
                                                        <option
                                                            v-for="(bankAccount,
                                                            bankIndex) in bankAccounts"
                                                            :value="bankAccount.id"
                                                            :key="bankIndex">
                                                            {{ `${bankAccount.bank.name} (${bankAccount.account_name})` }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Bank Extra Field End -->

                                        <!-- bkash Extra Field Start -->
                                        <div
                                            v-if="form.payment.method == 'bkash'">
                                            <div class="row">
                                                <label
                                                    class="text-right col-3 col-form-label required"
                                                    for="bank-name">
                                                    Bkash Number
                                                </label>

                                                <div class="col-9">
                                                    <input
                                                        type="text"
                                                        class="form-control form-control-sm"
                                                        required
                                                        v-model.trim="paymentInfo.phone_number"
                                                        placeholder="Enter bKash phone number"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- bkash Extra Field End -->

                                        <!-- Amount Start -->
                                        <div class="form-group row">
                                            <label
                                                class="text-right col-3 col-form-label"
                                                for="due-or-change">
                                                {{ dueOrChange >= 0 ? "Due" : "Return" }}
                                            </label>

                                            <div class="col-9">
                                                <div class="input-group input-group-sm">
                                                    <input
                                                        type="number"
                                                        class="form-control form-control-sm"
                                                        id="due-or-change"
                                                        autocomplete="off"
                                                        disabled
                                                        :value="
                                                            Number.parseFloat(
                                                                Math.abs(
                                                                dueOrChange
                                                            )
                                                            ).toFixed(2)"
                                                    />

                                                    <div class="input-group-append">
                                                        <span class="input-group-text">৳</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Amount End -->
                                    </div>
                                    <!-- Bottom Right Section End -->
                                </div>

                                <div class="pt-2 row border-top">
                                    <div class="col-md-6">
                                        <!-- Send SMS Start -->
                                        <div class="form-group row d-none">
                                            <div class="col-10 offset-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        id="client-send-sms"
                                                    />
                                                    <label
                                                        class="custom-control-label"
                                                        for="client-send-sms">
                                                        Send SMS
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Send SMS End -->
                                    </div>
                                    <div class="text-right col-md-6">
                                        <button :disabled="purchaseDisable" class="btn btn-sm btn-primary">
                                            Purchase
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Bottom Section End -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import "vue-select/dist/vue-select.css";
import lowestConverter from '../reusable/ConvertToLowestUnit'
import upperConverter from '../reusable/ConvertToUpperUnit'
export default {
    name: "PurchaseCreateComponent",
    props: {
        warehouses: Array,
        cashes: Array,
        parties: Array,
        bankAccounts: Array,
        products: Array,
    },
    watch: {
        selectedProducts: {
            deep: true,
            handler: function(value) {
                this.disableWarehouse = this.selectedProducts.length > 0;
                this.subtotal = value.reduce((total, item) => {
                    return parseFloat(item.total_price) + total;
                }, 0);
            }
        },
    },
    computed: {
        totalDiscount() {
            if(this.form.payment.discountType === 'flat') {
                return this.form.payment.discount || 0
            }else{
                return (
                    (parseFloat(this.subtotal || 0) * parseFloat(this.form.payment.discount || 0))
                    /
                    (parseFloat(100))
                )
            }
        },
        dueOrChange() {
            return (
                ((parseFloat(this.subtotal || 0)
                    + parseFloat(this.form.labourCost || 0)
                    + parseFloat(this.form.transportCost || 0))
                - parseFloat(this.partyBalance || 0))
                -
                (parseFloat(this.totalDiscount) || 0)
                -
                (parseFloat(this.form.payment.paid) || 0)
            );
        }
    },
    data() {
        return {
            searchCustomer: [],
            purchaseDisable: false,
            form: {
                labourCost: 0,
                transportCost: 0,
                date: new Date().toISOString().slice(0, 10),
                payment: {
                    discountType: 'flat',
                    discount: 0,
                    paid: null,
                    method: "cash"
                },
                note: null,
            },
            errors: null,
            disableWarehouse: false,
            barcode: null,
            warehouse_id: null,
            selectedProducts: [],
            selectedProduct: null,
            partyId: null,
            partyBalance: 0,
            subtotal: 0,
            paymentInfo: {
                cash_id: null,
                phone_number: null,
                bank_account_id: null,
            },
        };
    },

    mounted() {
        this.init();
    },

    methods: {
        onProductSelected(value) {
            if(!this.warehouse_id){
                this.$awn.alert('Please select warehouse!')
                return
            }
            const index = this.selectedProducts.findIndex(
                product => product.id === value.id
            );

            if (index === -1) {
                let warehouse = value.warehouses.find(warehouse => warehouse.id == this.warehouse_id)
                let displayQuantity
                if (warehouse){
                    displayQuantity = upperConverter(warehouse.stock.quantity, value.unit);
                }else{
                    displayQuantity = upperConverter(0, value.unit);
                }
                // default quantity
                this.selectedProducts.push(value);

                const unitRelation = value.unit.relation.split('/')
                let _quantity = [];
                for (let i = 0; i < unitRelation.length; i++) {
                    _quantity[i] = null
                }

                value.quantity_in_unit = _quantity

                const newProduct = {
                    ...value,
                    purchase_quantity: 0,
                    total_price: 0,
                    error: '',
                    display_quantity: displayQuantity || "No Stock Available",
                    purchasePrice: value.purchase_price,
                };

                this.selectedProducts.splice(
                    this.selectedProducts.length - 1,
                    1,
                    newProduct
                );

                this.selectedProduct = null;
            }else{
                this.$awn.warning(value.name +' already added in cart')
            }
        },

        addQuantity(event, productId, order) {
            let product = this.selectedProducts.find(product => product.id === productId)
            this.$set(product.quantity_in_unit, order, parseFloat(event.target.value));

            product.quantity_in_unit[order] = event.target.value ? event.target.value : null
            product.purchase_quantity = lowestConverter(product.quantity_in_unit, product.unit)

        },

        getPartyDetails(id) {
            this.partyBalance = this.parties.find(party => party.id == id).balance
        },

        purchaseConfirm() {
            let grandTotal = (
                (parseFloat(this.subtotal || 0)
                    + parseFloat(this.form.transportCost)
                    + parseFloat(this.form.labourCost))
                - parseFloat(this.totalDiscount)
            )

            let purchaseDue = parseFloat(grandTotal) - parseFloat(this.form.payment.paid);

            const form = {
                ...this.form,
                products: []
            };

            // for products
            let quantityError = false;
            this.selectedProducts.forEach(product => {
                if(product.purchase_quantity <= 0) {
                    quantityError = true
                    product.error = "Quantity can\'t be empty"
                }
                form.products.push({
                    id: product.id,
                    quantity: product.purchase_quantity,
                    quantity_in_unit: product.quantity_in_unit,
                    purchase_price: product.purchasePrice,
                    line_total: product.total_price
                });
            });

            if(quantityError) {
                form.products = []
                return
            }

            // for payments details
            switch (form.payment.method) {
                case "cash":
                    form.payment.purchase_payments = {
                        cash_id: this.paymentInfo.cash_id
                    };
                    break;
                case "bank":
                    form.payment.purchase_payments = {
                        bank_account_id: this.paymentInfo.bank_account_id,
                    };
                    break;
                case "bkash":
                    form.payment.purchase_payments = {
                        phone_number: this.paymentInfo.phone_number
                    };
                    break;

                default:
                    break;
            }
            // add subtotal
            form.payment.subtotal = this.subtotal;
            form.previous_balance = this.partyBalance;
            form.payment.total_discount = this.totalDiscount;

            form.party_id  = this.partyId;
            form.warehouse_id = this.warehouse_id;
            form.purchase_due = purchaseDue;

            // due or change
            if (this.dueOrChange > 0) {
                // due
                form.payment.due = Math.abs(this.dueOrChange);
            } else {
                // change
                form.payment.change = Math.abs(this.dueOrChange);
                form.payment.due = 0;
            }
            // this.purchaseDisable = true;

            this.$awn.asyncBlock(
                axios.post(baseURL + "user/purchase", form),
                response => {
                    // redirect to invoice
                    window.location.href =
                        baseURL +
                        "user/purchase/" +
                        response.data.id;
                    console.log(response.data)
                },
                reason => {
                    this.purchaseDisable = false;
                    console.log(reason.response.data)
                    this.errors = reason.response.data;
                }
            );
        },

        purchase() {
            // if ()
            let notifier = this.$awn;
            let onOk = () => this.purchaseConfirm();
            let onCancel = () => {notifier.info('Purchase Cancel', {durations: {info: 2000}})};
            notifier.confirm(
                'Are you sure to want purchase?',
                onOk,
                onCancel,
                {
                    labels: {
                        confirm: 'Purchase confirmation'
                    }
                }
            )
        },

        init() {
            try {
                // select first cash id
                // this.warehouse_id = this.warehouses[0].id
                this.paymentInfo.cash_id = (this.cashes.length > 0) ? this.cashes[0].id : null;
                // select first bank account
                this.paymentInfo.bank_account_id = (this.bankAccounts.length > 0) ? this.bankAccounts[0].id : null;
            } catch (error) {
                console.log(error);
            }
        }
    },

};
</script>

<style scoped>

</style>
