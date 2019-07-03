<?php


namespace ds1\admin_modules\human\entity;


abstract class base_entity
{
    protected function setProperties($prefix, $dbRow)
    {
        foreach ($dbRow as $key => $value)
        {
            $propertyKey = empty($prefix) ? $key : $prefix . '_' . $key;
            if (property_exists($this, $propertyKey))
            {
                $this->{$propertyKey} = $value;
            }
        }
    }
}