export default {
    namespaced: true,

    state() {
        return {
            blog: {},
            apiContext: {},
            loading: false
        };
    },

    getters: {
        isLoading(state) {
            return state.loading;
        }
    },

    mutations: {
        setApiContext(state, apiContext) {
            state.apiContext = apiContext;
        },

        setLoading(state, value) {
            state.loading = value;
        },

        setBlogId(state, blogId) {
            state.blogId = blogId;
        },

        setBlog(state, newBlog) {
            state.blog = newBlog;
        }
    }
}
