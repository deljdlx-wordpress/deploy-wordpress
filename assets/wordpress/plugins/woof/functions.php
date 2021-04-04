<?php
namespace Woof;
use Cocur\Slugify\Slugify;

function slugify($string)
{
    $slugify = new Slugify();
    return $slugify->slugify($string);
}
