import BlogDraftApiService from '../service/blog.draft.api.service';

Shopware.Application.addServiceProvider(BlogDraftApiService.name, (container) => {
    const initContainer = Shopware.Application.getContainer('init');
    return new BlogDraftApiService(initContainer.httpClient, container.loginService);
});
