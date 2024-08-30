<?php declare(strict_types=1);

namespace  EECom\EEComBlog\Core\Content\EEComBlog\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

class blogNotFoundException extends ShopwareHttpException
{
    public function __construct(string $blogid)
    {
        parent::__construct(
            'EECom Blog for id {{ blogid }} not found.',
            ['blogId' => $blogid]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__BLOG_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
