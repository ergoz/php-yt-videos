<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractCollection;

class Collection extends AbstractCollection
{
    public function instantiate($response)
    {
        //$this->id = $response->id;
        $this->title = (string) $response->title;
    }


}
