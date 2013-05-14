<?php

namespace Dukt\Videos\YouTube;

use Dukt\Videos\Common\AbstractUserInfos;

class UserInfos extends AbstractUserInfos
{
    public function instantiate($response)
    {

        $this->id = (string) $response->id;
        $this->id = substr($this->id, (strpos($this->id, ":user:") + 6));

        $this->name = (string) $response->author->name;

        // $this->id = $response->id;
        // $this->url = $response->url[0];
        // $this->title = $response->title;
        // $this->totalVideos = $response->total_videos;
    }
}

/*


object(SimpleXMLElement)#46 (8) {
  ["id"]=>
  string(48) "tag:youtube.com,2008:user:aVI3dGtWzG-dMNH1Rrx14A"
  ["published"]=>
  string(24) "2011-08-23T17:22:31.000Z"
  ["updated"]=>
  string(24) "2013-04-10T10:02:53.000Z"
  ["category"]=>
  object(SimpleXMLElement)#49 (1) {
    ["@attributes"]=>
    array(2) {
      ["scheme"]=>
      string(37) "http://schemas.google.com/g/2005#kind"
      ["term"]=>
      string(49) "http://gdata.youtube.com/schemas/2007#userProfile"
    }
  }
  ["title"]=>
  string(14) "Benjamin David"
  ["summary"]=>
  object(SimpleXMLElement)#50 (0) {
  }
  ["link"]=>
  array(4) {
    [0]=>
    object(SimpleXMLElement)#51 (1) {
      ["@attributes"]=>
      array(3) {
        ["rel"]=>
        string(9) "alternate"
        ["type"]=>
        string(9) "text/html"
        ["href"]=>
        string(56) "https://www.youtube.com/channel/UCaVI3dGtWzG-dMNH1Rrx14A"
      }
    }
    [1]=>
    object(SimpleXMLElement)#52 (1) {
      ["@attributes"]=>
      array(3) {
        ["rel"]=>
        string(51) "http://gdata.youtube.com/schemas/2007#insight.views"
        ["type"]=>
        string(9) "text/html"
        ["href"]=>
        string(328) "http://insight.youtube.com/video-analytics/csvreports?query=aVI3dGtWzG-dMNH1Rrx14A&type=u&starttime=1235865600000&endtime=1367712000000&user_starttime=1367107200000&user_endtime=1367712000000&region=world&token=KVqFSJQqOpuM1cUhS_JP6Xnp0MZ8MTM2Nzg0NzQ2NkAxMzY3ODQ1NjY2&hl=en_US&devKey=AWTS4U-3fPMQYGE59n8hmi4O88HsQjpE1a8d1GxQnGDm"
      }
    }
    [2]=>
    object(SimpleXMLElement)#53 (1) {
      ["@attributes"]=>
      array(3) {
        ["rel"]=>
        string(4) "self"
        ["type"]=>
        string(20) "application/atom+xml"
        ["href"]=>
        string(68) "https://gdata.youtube.com/feeds/api/users/aVI3dGtWzG-dMNH1Rrx14A?v=2"
      }
    }
    [3]=>
    object(SimpleXMLElement)#54 (1) {
      ["@attributes"]=>
      array(3) {
        ["rel"]=>
        string(4) "edit"
        ["type"]=>
        string(20) "application/atom+xml"
        ["href"]=>
        string(68) "https://gdata.youtube.com/feeds/api/users/aVI3dGtWzG-dMNH1Rrx14A?v=2"
      }
    }
  }
  ["author"]=>
  object(SimpleXMLElement)#55 (2) {
    ["name"]=>
    string(14) "Benjamin David"
    ["uri"]=>
    string(64) "https://gdata.youtube.com/feeds/api/users/aVI3dGtWzG-dMNH1Rrx14A"
  }
}

*/