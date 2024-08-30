<?php declare(strict_types=1);

namespace EECom\EEComBlog\Common;

use Throwable;

interface PublisherException extends Throwable
{
    public const EXCEPTION_CODE_PREFIX = 'EECOM_BLOG_PUBLISHER_';
}
