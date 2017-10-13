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
     * @param array $zone
     * @param string $server_ip_addr
     * @return array
     */
    public function setClientInfo($groupId = '', array $zone = null, $server_ip_addr = null)
    {
        $info = array('group_id' => $groupId);
        if ($zone != null) {
            $info['zone'] = $zone;
        }
        if ($server_ip_addr != null) {
            $info['server_ip_address'] = $server_ip_addr;
        }
        return $this->callPost(
            'setCientInfo',
            $info
        );
    }

    /**
     * @param $name
     * @return array
     */
    public function setGroupName($name)
    {
        return $this->callPost(
            'setGroupName',
            array('name' => $name)
        );
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
    public function setServerInfo($groupId, $type, $zone, array $client_list = array())
    {
        return $this->callPost(
            'setServerInfo',
            array('group_id' => $groupId, 'type' => $type, 'zone' => $zone, 'client_list' => $client_list)
        );
    }
}
