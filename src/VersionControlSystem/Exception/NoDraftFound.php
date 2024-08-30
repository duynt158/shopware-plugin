<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use EECom\EEComBlog\Common\PublisherException;
use Symfony\Component\HttpFoundation\Response;

class NoDraftFound extends ShopwareHttpException implements PublisherException
{
    public function __construct()
    {
        parent::__construct('Not a single draft found with that particular version');
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getErrorCode(): string
    {
        return self::EXCEPTION_CODE_PREFIX . 'NO_DRAFT_FOUND';
    }
}
