<?php


namespace ds1\admin_modules\human\entity;


class progress_collection
{
    private $entities;

    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    public function getByDefect($defectId)
    {
        $entities = [];

        /** @var progress_entity $entity */
        foreach ($this->entities as $entity)
        {
            if ($entity->defekt_obyvatele_id === $defectId)
            {
                $entities[] = $entity;
            }
        }

        return $entities;
    }
}