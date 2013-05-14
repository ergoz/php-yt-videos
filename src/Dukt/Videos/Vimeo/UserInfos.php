<?php

namespace Dukt\Videos\Vimeo;

use Dukt\Videos\Common\AbstractUserInfos;

class UserInfos extends AbstractUserInfos
{
    public function instantiate($response)
    {
        $this->id = $response->id;
        $this->name = $response->display_name;
    }
}









/*
object(stdClass)#43 (3) {
  ["generated_in"]=>
  string(6) "0.0498"
  ["stat"]=>
  string(2) "ok"
  ["person"]=>
  object(stdClass)#51 (23) {
    ["created_on"]=>
    string(19) "2006-04-02 19:09:50"
    ["id"]=>
    string(6) "110101"
    ["is_contact"]=>
    string(1) "0"
    ["is_plus"]=>
    string(1) "1"
    ["is_pro"]=>
    string(1) "0"
    ["is_staff"]=>
    string(1) "0"
    ["is_subscribed_to"]=>
    string(1) "0"
    ["username"]=>
    string(13) "benjamindavid"
    ["display_name"]=>
    string(14) "Benjamin David"
    ["location"]=>
    string(16) "The Alps, France"
    ["url"]=>
    array(1) {
      [0]=>
      string(0) ""
    }
    ["bio"]=>
    string(175) "I love playing guitar, taking photos, making videos, I don't like coffee, I like orange juice better.

ExpressionEngine Add-Ons : http://duktee.com
Company : http://dukt.fr"
    ["number_of_contacts"]=>
    string(2) "11"
    ["number_of_uploads"]=>
    string(2) "60"
    ["number_of_likes"]=>
    string(2) "10"
    ["number_of_videos"]=>
    string(2) "61"
    ["number_of_videos_appears_in"]=>
    string(1) "1"
    ["number_of_albums"]=>
    string(1) "4"
    ["number_of_channels"]=>
    string(1) "1"
    ["number_of_groups"]=>
    string(1) "1"
    ["profileurl"]=>
    string(30) "http://vimeo.com/benjamindavid"
    ["videosurl"]=>
    string(37) "http://vimeo.com/benjamindavid/videos"
    ["portraits"]=>
    object(stdClass)#52 (1) {
      ["portrait"]=>
      array(4) {
        [0]=>
        object(stdClass)#53 (3) {
          ["height"]=>
          string(2) "30"
          ["width"]=>
          string(2) "30"
          ["_content"]=>
          string(47) "http://b.vimeocdn.com/ps/182/864/1828642_30.jpg"
        }
        [1]=>
        object(stdClass)#54 (3) {
          ["height"]=>
          string(2) "75"
          ["width"]=>
          string(2) "75"
          ["_content"]=>
          string(47) "http://b.vimeocdn.com/ps/182/864/1828642_75.jpg"
        }
        [2]=>
        object(stdClass)#55 (3) {
          ["height"]=>
          string(3) "100"
          ["width"]=>
          string(3) "100"
          ["_content"]=>
          string(48) "http://b.vimeocdn.com/ps/182/864/1828642_100.jpg"
        }
        [3]=>
        object(stdClass)#56 (3) {
          ["height"]=>
          string(3) "300"
          ["width"]=>
          string(3) "300"
          ["_content"]=>
          string(48) "http://b.vimeocdn.com/ps/182/864/1828642_300.jpg"
        }
      }
    }
  }
}*/