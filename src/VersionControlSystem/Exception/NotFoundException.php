<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Exception;

use InvalidArgumentException;
use EECom\EEComBlog\Common\PublisherException;

class NotFoundException extends InvalidArgumentException implements PublisherException
{
}
