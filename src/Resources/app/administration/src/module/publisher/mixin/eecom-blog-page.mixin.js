const { Criteria } = Shopware.Data;

Shopware.Mixin.register('eecom-blog-page', {
    methods: {
        formatDraftSearchResult(draftSearchResult) {
            for (let i = 0; i < draftSearchResult.length; i++) {
                let currentDraft = draftSearchResult.getAt(i);

                currentDraft.categories = [];
                currentDraft.sections = [];
                currentDraft.extensions = {};

                if (!currentDraft.translated) {
                    currentDraft.translated = {
                        name: currentDraft.name
                    };
                }

                currentDraft.cms_page.getOrigin().previewMediaId = currentDraft.previewMediaId;
                currentDraft.cms_page.previewMediaId = currentDraft.previewMediaId;
                currentDraft.cms_page.previewMedia = currentDraft.previewMedia;
                currentDraft.cms_page.versionId = currentDraft.draftVersion;

                draftSearchResult[i] = currentDraft;
            }

            return draftSearchResult;
        },
    }
});
