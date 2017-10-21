<?php

namespace MusicCast\Api;

/**
 *
 *
 * @author Damien Surot <damien@toxeek.com>
 */

class Distribution extends AbstractApi
{
    /**
     * @return array
     */
    public function getDistributionInfo()
    {
        return $this->callGet('getDistributionInfo');
    }

    private function callGet($path)
    {
        return $this->get('/dist/' . $path);
    }

    /**
     * @param int $num
     * @return array
     */
    public function startDistribution($num = 0)
    {
        return $this->callGet('startDistribution?num=' . rawurlencode($num));
    }

    /**
     * @param string $groupId
     * @param string $zone
     * @param string $server_ip_addr
     * @return array
     */
    public function setClientInfo($groupId = '', $zone = 'main', $server_ip_addr = null)
    {
        $info = array('group_id' => $groupId);
        $info['zone'] = array($zone);
        if ($server_ip_addr != null) {
            $info['server_ip_address'] = $server_ip_addr;
        }
        return $this->callPost('setClientInfo', $info);
    }

    /**
     * @param $name
     * @return array
     */
    public function setGroupName($name)
    {
        return $this->callPost('setGroupName', array('name' => $name));
    }

    private function callPost($path, array $parameters = array())
    {
        return $this->post('/dist/' . $path, $parameters);
    }


    /**
     * @param $groupId
     * @param $type
     * @param $zone
     * @param array $client_list Clients IP
     * @return array
     */
    public function setServerInfo($groupId, $type = null, $zone = null, array $client_list = null)
    {
        $info = array('group_id' => $groupId);
        if ($type != null) {
            $info['type'] = $type;
        }
        if ($zone != null) {
            $info['zone'] = $zone;
        }
        if ($client_list != null) {
            $info['client_list'] = $client_list;
        }
        return $this->callPost('setServerInfo', array($info));
    }
}
