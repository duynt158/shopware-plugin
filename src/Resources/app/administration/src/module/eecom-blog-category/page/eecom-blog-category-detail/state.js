export default {
    namespaced: true,

    state() {
        return {
            loading: false,
            apiContext: {},
            category: {}
        };
    },

    getters: {
        isLoading(state) {
            return state.loading;
        }
    },

    mutations: {
        setLoading(state, value) {
            state.loading = value;
        },

        setApiContext(state, apiContext) {
            state.apiContext = apiContext;
        },

        setBlogCategory(state, newCategory) {
            state.category = newCategory;
        }
    }
}
