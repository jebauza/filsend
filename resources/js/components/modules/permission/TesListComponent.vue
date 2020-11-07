<template>
    <div class="card">
        

    </div>
</template>

<script>
import Multiselect from 'vue-multiselect';
import ProductFormAddEdit from './ProductFormAddEditComponent';

export default {
    components: {Multiselect,ProductFormAddEdit},
    created() {
        this.getProducts();
    },
    watch: {
        'searches.name': function (newValue, oldValue) {
            this.getProducts();
        },
        'searches.description': function (newValue, oldValue) {
            this.getProducts();
        },
        'searches.categories': function (newValue, oldValue) {
            this.getProducts();
        }
    },
    data() {
        return {
            products: {data:[]},

            allCategories: [],
            searches: {
                name: '',
                description: '',
                categories: []
            },

            fullscreenLoading: false,
            loaded: false
        }
    },
    methods: {
        getProducts(page = 1) {
            this.loaded = false;
            const url = `/cmsapi/configuration/products?page=${page}`;

            const {name, description, categories} = this.searches;
            axios.get(url, {
                params: {
                    name: name,
                    description: description,
                    categories: categories.map(item => item.id)
                }
            }).then(res => {
                this.getAllCategories();
                this.products = res.data;
                this.loaded = true;
            })
        },
        getAllCategories() {
            const url = `/cmsapi/configuration/categories/get-all-categories`;
            axios.get(url)
            .then(res => {
                this.allCategories = res.data;
            });
        },
        clearSearches() {
            this.searches = {
                name: '',
                description: '',
                categories: []
            };
        },
        openModalAddEdit(action, product = null) {
            this.$refs.productFormAddEdit.showForm(action, product);
        },
        updateProductList(action = null) {
            this.getProducts(this.products.current_page ?? 1 );
        },

    },

    computed: {
        authUserPermissions() {
            return JSON.parse(sessionStorage.getItem('listPermissionsByAuthUser'));
        }
    },

}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
