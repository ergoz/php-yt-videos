<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Videos\Common\AbstractCollection;

class Collection extends AbstractCollection
{
    public function instantiate($response)
    {
        $this->id = $response->id;
        $this->title = $response->title;
    }


}
