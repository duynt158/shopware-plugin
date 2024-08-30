const ROUTES = {
    SAVE_AS_DRAFT: 'draft',
    RELEASE_AS_NEW: 'releaseAsNew',
    DISCARD: 'discard',
    MERGE: 'merge',
    DUPLICATE: 'duplicate',
    UPDATE_FROM_LIVE_VERSION: 'updateFromLiveVersion'
};

export default class BlogDraftApiService extends Shopware.Classes.ApiService {
    constructor(httpClient, loginService, apiEndpoint = '_action') {
        super(httpClient, loginService, apiEndpoint);
    }

    static get name() {
        return 'blogDraftApiService'
    }

    saveAsDraft(entity, name) {

        const apiPath = this.getApiPath(entity, ROUTES.SAVE_AS_DRAFT);
        const headers = this.getBasicHeaders({});

        return this.httpClient.post(apiPath, { name }, { headers }).then((response) => {
            return Shopware.Classes.ApiService.handleResponse(response);
        });
    }

    releaseAsNew(entity) {
        const apiPath = this.getApiPath(entity, ROUTES.RELEASE_AS_NEW);
        const headers = this.getBasicHeaders({});

        return this.httpClient.post(apiPath, null, { headers }).then((response) => {
            return Shopware.Classes.ApiService.handleResponse(response);
        });
    }

    discard(entity) {
        const apiPath = this.getApiPath(entity, ROUTES.DISCARD);
        const headers = this.getBasicHeaders({});

        return this.httpClient.post(apiPath, null, { headers }).then((response) => {
            return Shopware.Classes.ApiService.handleResponse(response);
        });
    }

    merge(entity) {
        const apiPath = this.getApiPath(entity, ROUTES.MERGE);
        const headers = this.getBasicHeaders({});

        return this.httpClient.post(apiPath, null, { headers }).then((response) => {
            return Shopware.Classes.ApiService.handleResponse(response);
        });
    }

    duplicate(entity) {
        const apiPath = this.getApiPath(entity, ROUTES.DUPLICATE);
        const headers = this.getBasicHeaders({});

        return this.httpClient.post(apiPath, null, { headers }).then((response) => {
            return Shopware.Classes.ApiService.handleResponse(response);
        });
    }

    updateFromLiveVersion(entity) {
        const apiPath = this.getApiPath(entity, ROUTES.UPDATE_FROM_LIVE_VERSION);
        const headers = this.getBasicHeaders({});

        return this.httpClient.post(apiPath, null, { headers }).then((response) => {
            return Shopware.Classes.ApiService.handleResponse(response);
        });
    }

    getApiPath(entity, action) {
        let path = `${this.apiEndpoint}/${entity.getEntityName()}/${entity.id}/${action}`;

        if (action !== ROUTES.SAVE_AS_DRAFT) {
            path += `/${entity.versionId}`;
        }

        return path;
    }


}
