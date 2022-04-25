<template>
    <div class="card">
        <!-- Header Start -->
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Update Product</h5>
        </div>
        <!-- Header End -->

        <!-- Body Start -->
        <div class="card-body p-0">
            <div class="col-12 py-2">
                <form action="" method="POST" @submit.prevent="saveProduct">
                    <div class="form-row">
                        <div class="col-md-8 pr-5">
                            <div class="form-row">
                                <!-- Brand Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="brand">Brand</label>
                                    <select
                                        name="brand_id"
                                        v-model="brandId"
                                        class="form-control"
                                        id="brand"
                                    >
                                        <option :value="null" selected disabled
                                            >Select Brand</option
                                        >
                                        <option
                                            v-for="(brand, index) in brands"
                                            :value="brand.id"
                                            :key="index"
                                            >{{ brand.name }}</option
                                        >
                                    </select>
                                </div>
                                <!-- Brand End -->

                                <!-- Category Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="category">Category</label>
                                    <select
                                        name="category_id"
                                        v-model="categoryId"
                                        class="form-control"
                                        id="category"
                                    >
                                        <option :value="null" selected disabled
                                            >Select Category</option
                                        >
                                        <option
                                            v-for="(category,
                                            index) in categories"
                                            :value="category.id"
                                            :key="index"
                                            >{{ category.name }}</option
                                        >
                                    </select>
                                </div>
                                <!-- Category End -->

                                <!-- Product Name Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="name">Product Name</label>
                                    <input
                                        type="text"
                                        v-model="name"
                                        class="form-control"
                                        id="name"
                                        placeholder="Enter product name"
                                    />
                                </div>
                                <!-- Product Name Start -->

                                <!-- Barcode Start -->
<!--                                <div class="form-group col-md-6 required">-->
<!--                                    <label for="name">Barcode</label>-->
<!--                                    <input-->
<!--                                        type="text"-->
<!--                                        v-model="barcode"-->
<!--                                        class="form-control"-->
<!--                                        id="barcode"-->
<!--                                    />-->
<!--                                </div>-->
                                <!-- Barcode End -->

                                <!-- Purchase Price Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="purchase-price"
                                        >Purchase Price</label
                                    >
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                >BDT</span
                                            >
                                        </div>
                                        <input
                                            type="number"
                                            v-model="purchasePrice"
                                            step="any"
                                            id="purchase-price"
                                            class="form-control"
                                            placeholder="Enter purchase price"
                                        />
                                    </div>
                                </div>
                                <!-- Purchase Price End -->

                                <!-- Dealer Price Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="dealer-price"
                                        >Dealer Price</label
                                    >
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"
                                                >BDT</span
                                            >
                                        </div>
                                        <input
                                            type="number"
                                            v-model="dealerPrice"
                                            step="any"
                                            required
                                            id="dealer-price"
                                            class="form-control"
                                            placeholder="Enter dealer price"
                                        />
                                    </div>
                                </div>
                                <!-- Dealer Price End -->

                                <!-- Sub Dealer Price Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="sub-dealer-price">Sub Dealer Price</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">BDT</span>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="subDealerPrice"
                                            step="any"
                                            required
                                            id="sub-dealer-price"
                                            class="form-control"
                                            placeholder="Enter sub dealer price"/>
                                    </div>
                                </div>
                                <!-- Sub Dealer Price End -->

                                <!-- Cash Customer Price Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="cash-customer-price">Cash Customer Price</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">BDT</span>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="cashCustomerPrice"
                                            step="any"
                                            required
                                            id="cash-customer-price"
                                            class="form-control"
                                            placeholder="Enter cash customer price"/>
                                    </div>
                                </div>
                                <!-- Cash Customer Price End -->

                                <!-- Semi Cash Customer Price Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="semi-cash-customer-price">Semi Cash Customer Price</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">BDT</span>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="semiCashCustomerPrice"
                                            step="any"
                                            required
                                            id="semi-cash-customer-price"
                                            class="form-control"
                                            placeholder="Enter semi cash customer price"/>
                                    </div>
                                </div>
                                <!-- Semi Cash Customer Price End -->

                                <!-- One Third Customer Price Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="one-third-customer-price">One Third Customer Price</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">BDT</span>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="oneThirdCustomerPrice"
                                            step="any"
                                            required
                                            id="one-third-customer-price"
                                            class="form-control"
                                            placeholder="Enter one third customer price"/>
                                    </div>
                                </div>
                                <!-- One Third Customer Price End -->

                                <!-- Semi Credit Price Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="semi-credit-price">Semi Credit Price</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">BDT</span>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="semiCreditPrice"
                                            step="any"
                                            required
                                            id="semi-credit-price"
                                            class="form-control"
                                            placeholder="Enter semi credit price"/>
                                    </div>
                                </div>
                                <!-- Semi Credit Price End -->

                                <!-- Regular Credit Price Start -->
                                <div class="form-group col-md-6 required">
                                    <label for="regular-credit-price">Regular Credit Price</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">BDT</span>
                                        </div>
                                        <input
                                            type="number"
                                            v-model="regularCreditPrice"
                                            step="any"
                                            required
                                            id="regular-credit-price"
                                            class="form-control"
                                            placeholder="Enter regula credit price"/>
                                    </div>
                                </div>
                                <!-- Regular Credit Price End -->

                                <!-- Stock Alert Start -->
                                <div class="form-group col-md-6">
                                    <label for="stockAlert">Stock Alert</label>
                                    <input
                                        type="text"
                                        v-model="stockAlert"
                                        class="form-control"
                                        id="stockAlert"
                                        placeholder="Enter stock alert"
                                    />
                                </div>
                                <!-- Stock Alert End -->
                            </div>

                            <!-- Description Start -->
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea
                                    class="form-control"
                                    v-model="description"
                                    placeholder="Enter product description"
                                    id="description"
                                    rows="5"
                                ></textarea>
                            </div>
                            <!-- Description End -->
                        </div>

                        <!-- Unit Start -->
                        <div class="col-md-4">
                            <div class="form-group col-md-12">
                                <div class="form-row">
                                    <div class="form-group col-md-12 required">
                                        <label for="unit">Unit</label>
                                        <select
                                            v-model="unit"
                                            @change="changeUnit"
                                            class="form-control"
                                            id="unit"
                                        >
                                            <option
                                                :value="null"
                                                selected
                                                disabled
                                                >Select Unit</option
                                            >
                                            <option
                                                v-for="(unit, index) in units"
                                                :value="unit"
                                                :key="index"
                                                >{{ unit.name }} ({{
                                                    unit.description
                                                }})</option
                                            >
                                        </select>
                                    </div>

                                    <div class="form-group col-md-12">
                                        <div
                                            v-for="(warehouse,
                                            index) in warehouses"
                                            :key="index"
                                            class="card mb-2"
                                        >
                                            <div class="card-header">
                                                {{ warehouse.title }}
                                            </div>

                                            <div class="card-body p-0">
                                                <input
                                                    type="number"
                                                    v-if="!parsedUnits.length"
                                                    class="form-control"
                                                    placeholder="Select Unit First"
                                                    disabled
                                                />
                                                <div
                                                    class="input-group required"
                                                    v-else
                                                    v-for="(unit,
                                                    unitIndex) in parsedUnits"
                                                    :key="unitIndex"
                                                >
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        :value="
                                                            quantity[
                                                                warehouse.id
                                                            ]
                                                                ? quantity[
                                                                      warehouse
                                                                          .id
                                                                  ][unitIndex]
                                                                    ? quantity[
                                                                          warehouse
                                                                              .id
                                                                      ][
                                                                          unitIndex
                                                                      ]
                                                                    : ''
                                                                : ''
                                                        "
                                                        @keyup="
                                                            addQuantity(
                                                                $event,
                                                                warehouse.id,
                                                                unitIndex
                                                            )
                                                        "
                                                        @blur="
                                                            addQuantity(
                                                                $event,
                                                                warehouse.id,
                                                                unitIndex
                                                            )
                                                        "
                                                        @change="
                                                            addQuantity(
                                                                $event,
                                                                warehouse.id,
                                                                unitIndex
                                                            )
                                                        "
                                                        class="form-control"
                                                        :placeholder="
                                                            'Enter ' +
                                                                unit.toLowerCase() +
                                                                ' amount'
                                                        "
                                                    />
                                                    <div class="input-group-append text-right">
                                                        <span class="input-group-text" style="min-width: 100px;">{{ unit }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Unit End -->
                    </div>

                    <div class="text-right">
                        <button
                            type="button"
                            @click.prevent="initValues()"
                            class="btn btn-danger"
                        >
                            Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Body Start -->
    </div>
</template>

<script>
export default {
    name: "UpdateProductComponent",
    props: {
        product: {
            type: Object,
            required: true
        },
        units: {
            type: Array,
            required: true
        },
        warehouses: {
            type: Array,
            required: true
        },
        extras: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            supplierId: null,
            brands: [],
            brandId: null,
            categoryId: null,
            categories: [],
            unit: null,
            parsedUnits: [],
            /*----*/
            name: null,
            barcode: null,
            purchasePrice: null,
            dealerPrice: null,
            subDealerPrice: null,
            cashCustomerPrice: null,
            semiCashCustomerPrice: null,
            oneThirdCustomerPrice: null,
            semiCreditPrice: null,
            regularCreditPrice: null,
            stockAlert: null,
            vat: 0,
            description: null,
            quantity: []
        };
    },
    methods: {
        changeUnit(event) {
            if (!this.unit) {
                this.parsedUnits = [];
                return;
            }
            this.quantity = {};
            this.parsedUnits = this.unit.labels.split("/");
        },
        addQuantity(event, warehouseId, order) {
            if (!(warehouseId in this.quantity)) {
                this.$set(this.quantity, warehouseId, {});
            }
            this.$set(this.quantity[warehouseId], order, event.target.value);
        },
        saveProduct() {
            this.$awn.asyncBlock(
                axios.patch(baseURL + "user/product/" + this.product.id, {
                    brand_id: this.brandId,
                    category_id: this.categoryId,
                    name: this.name,
                    barcode: this.barcode,
                    purchase_price: this.purchasePrice,
                    dealer_price: this.dealerPrice,
                    sub_dealer_price: this.subDealerPrice,
                    cash_customer_price: this.cashCustomerPrice,
                    semi_cash_customer_price: this.semiCashCustomerPrice,
                    one_third_customer_price: this.oneThirdCustomerPrice,
                    semi_credit_price: this.semiCreditPrice,
                    regular_credit_price: this.regularCreditPrice,
                    description: this.description,
                    stock_alert: this.stockAlert,
                    unit_id: this.unit.id,
                    /*-----------*/
                    quantity: this.quantity
                }),
                response => {
                    //console.log(response.data)
                    location.href = response.data;
                },
                error => {
                    this.$awn.alert(
                        "Opps! something went wrong. Try again later"
                    );
                }
            );
        },
        initValues() {
            //set option
            this.brands = this.extras.brands;
            this.categories = this.extras.categories;
            //set value
            this.supplierId = this.product.party_id;
            this.brandId = this.product.brand_id;
            this.categoryId = this.product.category_id;
            this.model = this.product.model;
            this.name = this.product.name;
            this.barcode = this.product.barcode;
            this.purchasePrice = this.product.purchase_price;
            this.dealerPrice = this.product.dealer_price;
            this.subDealerPrice = this.product.sub_dealer_price
            this.cashCustomerPrice = this.product.cash_customer_price
            this.semiCashCustomerPrice = this.product.semi_cash_customer_price
            this.oneThirdCustomerPrice = this.product.one_third_customer_price
            this.semiCreditPrice = this.product.semi_credit_price
            this.regularCreditPrice = this.product.regular_credit_price
            this.stockAlert = this.product.stock_alert;
            this.vat = this.product.vat;
            this.description = this.product.description;
            this.unit = this.extras.unit;
            this.changeUnit();
            if (!(this.extras.quantity.length === 0)) {
                //if it has quantity then assign
                this.quantity = this.extras.quantity;
            }
        }
    },
    mounted() {
        this.initValues();
    }
};
</script>

<style scoped></style>
