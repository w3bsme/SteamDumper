<?php

/**
 * SteamDumper v1.5.1
 *
 * @version 1.5.1
 * @author @w3bsme https://github.com/w3bsme
 * @copyright https://github.com/w3bsme/SteamDumper/blob/master/LICENSE
 * @source https://github.com/w3bsme/SteamDumper
 */

Class Settings
{
    /**
     * Your Steam Web API Key
     * @var string
     * @link https://steamcommunity.com/dev/apikey Steam Web API Key
     */
    const STEAM_DEV_APIKEY = '';
}

Class SteamDumper extends Settings
{
    const REGEXP = '/<a\s+.+\s+.+\s+href="(.+?)"\s+data\-miniprofile="\d+">\s+<bdi>(.+?)<\/bdi><\/a>\s+<span\s+class="commentthread_comment_timestamp"\s+title=".+"\s+data-timestamp="(.+?)">\s+.+\s+<\/span>\s+.+\s+.+\s+.+\s+<div\s+class="commentthread_comment_text"\s+id="comment_content_\d+">\s+(.+?)\s+<\/div>/';

    private $steamID64 = 0;

    public function setSteamID64($value)
    {
        $this->steamID64 = $value;
    }

    public function getSteamID64()
    {
        return $this->steamID64;
    }

    public function resolve($value)
    {
        $this->setSteamID64(json_decode(file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . self::STEAM_DEV_APIKEY . "&steamids=" . $value), true)['response']['players'][0]['steamid'] ?? 0);
        if (gettype($this->getSteamID64()) !== "integer" && gettype($this->getSteamID64()) === "string")
        {
            /**
             * If it is steamID64, just rewrite it as integer (its type)
             * @param string (explicit type)
             * @param int (implicit type)
             */
            $this->setSteamID64((int) $this->getSteamID64());
        } else {
            /**
            * If it’s not steamID64, then we get steamID64 via customURL
            * @param string (explicit type)
            * @param int (implicit type)
            */
            $this->setSteamID64((int) simplexml_load_string(file_get_contents('https://steamcommunity.com/id/' . $value . '/?xml=1'))->steamID64 ?? 0);
        }
        return $this->getSteamID64();
    }

    public function init($value)
    {
        $this->resolve($value);
        if (gettype($this->getSteamID64()) === "integer" && $this->getSteamID64() !== 0)
        {
            $json = json_decode(file_get_contents('https://steamcommunity.com/comment/profile/render/' . $this->getSteamID64()));
            if ($json->comments_html)
            {
                preg_match_all(self::REGEXP, $json->comments_html, $matches, PREG_PATTERN_ORDER);
                unset($json);
                $result = (object) [
                    "response" => [
                        "vanityurl" => $matches[1],
                        "personaname" => $matches[2],
                        "unixtime" => $matches[3],
                        "comments_html" => $matches[4]
                    ]
                ];
                return $result;
            } else {
                echo json_encode([
                    "response" => [
                        0 => [
                            "error" => "Comments not found",
                            "description" => "User has no profile comments",
                            "error_code" => 1
                        ]
                    ]]
                );
            }
        } else {
            echo json_encode([
                "response" => [
                    0 => [
                        "error" => "Wrong customURL or SteamID64",
                        "description" => "Isn't set customURL or SteamID64",
                        "error_code" => 2
                    ]
                ]]
            );
        }
    }

    /**
     * Trying to get the last comment
     * @param  String $value SteamID64 or customURL
     * @return Object        Result
     */
    public function getLastComment(String $value = null) : Object
    {
        $obj = [];
        foreach ($this->init($value)->response as $key => $value)
        {
            array_push($obj, reset($value));
        }
        return !empty($obj) ? (Object) $obj : (Object) [];
    }

    /**
     * Trying to get the last ten comment
     * @param  String $value SteamID64 or customURL
     * @return Object        Result
     */
    public function getTenLastComments(String $value = null) : Object
    {
        return !empty($value) ? (Object) $this->init($value)->response : (Object) [];
    }
}

$SteamDumper = new SteamDumper;
