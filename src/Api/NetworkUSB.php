<?php


namespace MusicCast\Api;

/**
 * APIs in regard to Network/USB related setting and getting information
 * Target Inputs: USB / Network related ones (Server / Net Radio / Pandora / Spotify / AirPlay etc.)
 *
 * @author Sam Van der Borght <samvanderborght@gmail.com>
 * @author Damien Surot <damien@toxeek.com>
 */
class NetworkUSB extends AbstractApi
{

    /**
     * For retrieving preset information. Presets are common use among Net/USB related input sources
     *
     * @return array
     */
    public function getPresetInfo()
    {
        return $this->callGet('getPresetInfo');
    }

    /**
     *For retrieving playback information
     *
     * @return array
     */
    public function getPlayInfo()
    {
        return $this->callGet('getPlayInfo');
    }

    /**
     * For controlling playback status
     *
     * @param string $playback Specifies playback status
     * Values: "play" / "stop" / "pause" / "play_pause" / "previous" / "next" /
     * "fast_reverse_start" / "fast_reverse_end" / "fast_forward_start" /
     * "fast_forward_end"
     * @return array
     */
    public function setPlayback($playback)
    {
        return $this->callGet('setPlayback?playback=' . rawurlencode($playback));
    }

    /**
     * For toggling repeat setting. No direct / discrete setting commands available
     *
     * @return array
     */
    public function toggleRepeat()
    {
        return $this->callGet('toggleRepeat');
    }

    /**
     * For toggling shuffle setting. No direct / discrete setting commands available
     *
     * @return array
     */
    public function toggleShuffle()
    {
        return $this->callGet('toggleShuffle');
    }

    /**
     * For retrieving list information. Basically this info is available to all relevant inputs, not limited to
     * or independent from current input
     *
     * @param string $list_id Specifies list ID. If nothing specified, "main" is chosen implicitly
     * Values: "main" (common for all Net/USB sources)
     * "auto_complete" (Pandora)
     * "search_artist" (Pandora)
     * "search_track" (Pandora)
     *
     * @param string $input Specifies target Input ID. Controls for setListControl are to work
     * with the input specified here
     * Values: Input IDs for Net/USB related sources
     *
     * @param integer $index Specifies the reference index (offset from the beginning of the list).
     * Note that this index must be in multiple of 8. If nothing was
     * specified, the reference index previously specified would be used
     * Values: 0, 8, 16, 24, ..., 64984, 64992
     *
     * @param integer $size Specifies max list size retrieved at a time
     * Value Range: 1 - 8
     *
     * @param string $lang Specifies list language. But menu names or text info are not
     * always necessarily pulled in a language specified here. If nothing
     * specified, English ("en") is used implicitly
     * Values: "en" (English)/ "ja" (Japanese)/ "fr" (French)/ "de"
     * (German)/ "es" (Spanish)/ "ru" (Russian)/ "it" (Italy)/ "zh" (Chinese)
     *
     * @return array
     */
    public function getListInfo($input, $size, $list_id = 'main', $index = null, $lang = null)
    {
        return $this->callGet('getListInfo?list_id=' . rawurlencode($list_id) . '&input=' . rawurlencode($input) .
            (isset($index) ? '&index=' . rawurlencode($index) : '') . '&size=' . rawurlencode($size) .
            (isset($lang) ? '&lang=' . rawurlencode($lang) : ''));
    }

    /**
     * For control a list. Controllable list info is not limited to or independent from current input
     *
     * @param string $list_id Specifies list ID. If nothing specified, "main" is chosen implicitly
     * Values: "main" (common for all Net/USB sources)
     * "auto_complete" (Pandora)
     * "search_artist" (Pandora)
     * "search_track" (Pandora)
     *
     * @param string $type Specifies list transition type.
     * "select" is to enter and get into one deeper layer than the current layer where the element specified by
     * the index belongs to.
     * "play" is to start playback current index element,
     * "return" is to go back one upper layer than current.
     * "select" and "play" needs to specify an index at the same time.
     * In case to “select” an element with its attribute being "Capable of Search", specify search text using
     * setSearchString in advance. (Or it is possible to specify search text and move layers at the same
     * time by specifying an index in setSearchString)
     * Values: "select" / "play" / "return"
     *
     * @param integer $index Specifies the reference index (offset from the beginning of the list).
     * Note that this index must be in multiple of 8. If nothing was
     * specified, the reference index previously specified would be used
     * Values: 0, 8, 16, 24, ..., 64984, 64992
     *
     * @param string $zone Specifies target zone to playback. In the specified zone, input
     * change occurs at the same time of playback.
     * This parameter is valid only when type "play" is specified. If
     * nothing is specified, "main" is chosen implicitly
     * Values:  : "main" / "zone2" / "zone3" / "zone4"
     *
     * @return array
     */
    public function setListControl($type, $list_id = 'main', $index = null, $zone = null)
    {
        return $this->callGet('setListControl?list_id=' . rawurlencode($list_id) . '&type=' . rawurlencode($type) .
            (isset($index) ? '&index=' . rawurlencode($index) : '') .
            (isset($zone) ? '&zone=' . rawurlencode($zone) : ''));
    }

    /**
     * For setting search text. Specifies string executing this API before select an element with its
     * attribute being “Capable of Search” or retrieve info about searching list(Pandora).
     *
     * @param string $list_id Specifies list ID. If nothing specified, "main" is chosen implicitly
     * Values: "main" (common for all Net/USB sources)
     * "auto_complete" (Pandora)
     * "search_artist" (Pandora)
     * "search_track" (Pandora)
     *
     * @param string $string Setting search text
     *
     * @param integer $index Specifies an element position in the list being selected (offset from
     * the beginning of the list).Valid only when the list_id is "main"
     * Specifies index an element with its attribute being "Capable of
     * Search" Controls same as setListControl "select" are to work with
     * the index an element specified. If no index is specified, non-actions
     * of select
     * Values : 0 ~ 64999
     * @return array
     */
    public function setSearchString($string, $list_id = 'main', $index = null)
    {
        return $this->callGet('setSearchString?list_id=' . rawurlencode($list_id) . '&string=' . rawurlencode($string) .
            (isset($index) ? '&index=' . rawurlencode($index) : ''));
    }

    /**
     * For recalling a content preset
     *
     * @param string $zone Specifies station recalling zone. This causes input change in specified zone
     *          Values: "main" / "zone2" / "zone3" / "zone4"
     *
     * @param integer $num Specifies Preset number
     *          Value: one in the range gotten via /system/getFeatures
     * @return array
     */
    public function recallPreset($zone, $num)
    {
        return $this->callGet('recallPreset?zone=' . rawurlencode($zone) . '&num=' . rawurlencode($num));
    }

    /**
     * For registering current content to a preset. Presets are common use among Net/USB related input sources.
     *
     * @param integer $num Specifying a preset number
     *             Value: one in the range gotten via /system/getFeatures
     *
     * @return array
     */
    public function storePreset($num)
    {
        return $this->callGet('storePreset?num=' . rawurlencode($num));
    }

    /**
     * For retrieving account information registered on Device
     *
     * @return array
     */
    public function getAccountStatus()
    {
        return $this->callGet('getAccountStatus');
    }

    /**
     * For switching account for service corresponding multi account
     *
     * @param string $input Specifies target Input ID.
     *          Value: "pandora"
     * @param integer $index Specifies switch account index
     *          Value : 0 ~ 7 (Pandora)
     * @param integer $timeout Specifies timeout duration(ms) for this API process. If specifies 0,
     * treat as maximum value.
     *          Value: 0 ～ 60000
     * @return array
     */
    public function switchAccount($input, $index, $timeout)
    {
        return $this->callGet('switchAccount?input=' . rawurlencode($input) . '&index=' . rawurlencode($index)
            . '&timeout=' . rawurlencode($timeout));
    }

    /**
     * @param $input
     * @param $type
     * @param $timeout
     * @return array|string
     */
    public function getServiceInfo($input, $type, $timeout)
    {
        return $this->callGet('getServiceInfo?input=' . rawurlencode($input) . '&type=' . rawurlencode($type)
            . '&timeout=' . rawurlencode($timeout));
    }

    /**
     * @return array
     */
    public function getMcPlaylistName()
    {
        return $this->callGet('getMcPlaylistName');
    }

    /**
     * @param $bank
     * @param $type
     * @param int $index
     * @param string $zone
     * @return array|string
     */
    public function manageMcPlaylist($bank, $type, $index = 0, $zone = 'main')
    {
        return $this->callGet('manageMcPlaylist?bank=' . rawurlencode($bank) . '&type=' . rawurlencode($type)
            . '&index=' . rawurlencode($index) . '&zone=' . rawurlencode($zone));
    }

    /**
     * @param $bank
     * @param int $index
     * @return array|string
     */
    public function getMcPlaylist($bank, $index = 0)
    {
        return $this->callGet('getMcPlaylist?bank=' . rawurlencode($bank) . '&index=' . rawurlencode($index));
    }

    /**
     * @param integer $index
     * @return array
     */
    public function getPlayQueue($index = 0)
    {
        return $this->callGet('getPlayQueue?index=' . rawurlencode($index));
    }

    /**
     * @return array
     */
    public function getRecentInfo()
    {
        return $this->callGet('getRecentInfo');
    }

    /**
     * @param $zone
     * @param $uri
     * @return array
     */
    public function setYmapUri($zone, $uri)
    {
        return $this->callPost('setYmapUri', array('zone' => $zone, "uri" => $uri));
    }


    private function callGet($path)
    {
        return $this->get('/netusb/' . $path);
    }

    private function callPost($path, array $parameters = array())
    {
        return $this->post('/netusb/' . $path, $parameters);
    }
}
