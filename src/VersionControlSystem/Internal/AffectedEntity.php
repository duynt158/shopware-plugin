<?php declare(strict_types=1);

namespace EECom\EEComBlog\VersionControlSystem\Internal;

class AffectedEntity
{
    private string $id;

    private string $name;

    private function __construct(
        string $id,
        string $name
    )
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function create(string $id, string $name): self
    {
        return new AffectedEntity($id, $name);
    }
}
