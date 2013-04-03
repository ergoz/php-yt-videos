<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractCollection;

class Collection extends AbstractCollection
{
    public function instantiate($response)
    {
        $id = (string) $response->id;

        $id = substr($id, strpos($id, "playlist:") + 9);

        if(strpos($id, ":") !== false)
        {
            $id = substr($id, 0, strpos($id, ":"));
        }


        $this->id = $id;
        $this->title = (string) $response->title;
    }


}
