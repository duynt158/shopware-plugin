<?php declare(strict_types=1);

namespace EECom\EEComBlog\Core\Content\EEComBlog\Events;

interface EEComBlogChangedEventInterface
{
    public function getIds(): array;
}
