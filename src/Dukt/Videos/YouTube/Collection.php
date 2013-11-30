<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractCollection;

class Collection extends AbstractCollection
{
    public function instantiate($response)
    {
        $id = (string) $response->id;

        $id = substr($id, strpos($id, "playlist:") + 9);

        if(strpos($id, ":") !== false) {
            $id = substr($id, 0, strpos($id, ":"));
        }

        $yt = $response->children('http://gdata.youtube.com/schemas/2007');

        $this->id = $id;
        $this->title = (string) $response->title;
        $this->totalVideos = (int) $yt->countHint;

        // url

        $this->url = null;

        foreach($response->link as $k => $v) {
            if($v['type'] == 'text/html') {
                $this->url = (string) $v['href'];
            }
        }
    }
}
