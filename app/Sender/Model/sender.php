<?php
/**
 * @package ly.
 * @author Supme
 * @copyright Supme 2014
 * @license http://opensource.org/licenses/MIT MIT License	
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

namespace App\Sender\Model;

class sender extends \Table
{
    public $campaignId;

    /**
     * Get campaign list
     * @param $count
     * @param $from
     * @return array|bool
     */
    function getCampaigns( $lenght, $start = 0 )
    {
        return $this->select(
            'sender_campaigns',
            '*',
            [
                'ORDER' => 'date DESC',
                'LIMIT' => [$start, $lenght]
            ]
        );
    }


    /**
     * Add or update campaign
     *
     * @param string $name
     * @param string $subject
     * @param string $message
     * @param $id
     * @param $time
     * @return int
     */
    function campaign( $name, $subject, $message = '', $id = false, $time = false )
    {
        if ($time == false) $time = time();
        if ($id != false ) {
            $result = $this->update( 'sender_campaigns',
                [
                    'name' => $name,
                    'subject' => $subject,
                    'message' => $message,
                    'time' => $time,
                ],
                [
                    'id' => $id,
                ]
            );
        } else {
            $result = $this->insert( 'sender_campaigns',
                [
                    'name' => $name,
                    'subject' => $subject,
                    'message' => $message,
                    'time' => $time,
                ]
            );
        }

        return $result;
    }


    /**
     * Get recipient list from campaign
     *
     * @param $count
     * @param int $from
     * @return array|bool
     */
    function getRecipients( $lenght, $start = 0 )
    {

        $data =  $this->select(
            'sender_recipient',
            ["id"],
            ['campaign_id' => $this->campaignId, 'LIMIT' => [$start, $lenght]]);
        $recipient = [];
        foreach($data as $id){
            $params = $this->select(
                'sender_params',
                [
                    'name',
                    'value',
                ],
                ['recipient_id' => $id['id']]
            );
            foreach($params as $param){
                $recipient[$id['id']][$param['name']] = $param['value'];
            }

        }

        return $recipient;
    }

    /**
     * Insert recipients in campaign list
     *
     * @param $campaignId integer
     * @param $data array
     *                    [
     *                      [
     *                        parameterName => parameterValue
     *                        ...
     *                      ]
     *                      ...
     *                    ]
     */
    function addRecipients( $data )
    {
        if( is_array( $this->select( 'sender_campaigns', '*', ['id' => $this->campaignId] ) ) ){
            foreach($data as $recipient){
                $id = $this->insert('sender_recipient',['campaignId'=>$this->campaignId]);
                foreach($recipient as $name => $value){
                    $this->insert( 'sender_params',
                        [
                            'recipient_id' => $id,
                            'name' => $name,
                            'value' => $value,
                        ]
                    );
                }
            }
        }
    }

    function column()
    {
        $data = $this->query(
                'SELECT DISTINCT sp.name
                  FROM sender_params sp
                  INNER JOIN sender_recipient sr
                  ON sr.id=sp.recipient_id
                  AND sr.campaign_id='.$this->quote((int)$this->campaignId)
            )
            ->fetchAll();
        $result = [];
        foreach($data as $column){
            $result[] = $column['name'];
        }

        return $result;
    }



    function data( $start, $lenght, $order, $filter )
    {
        $data =  $this->select(
            'sender_recipient',
            ["id"],
            ['campaign_id' => $this->campaignId, 'LIMIT' => [$start, $lenght]]);
        $recipient = [];
        $i = 0;
        foreach($data as $id){
            $params = $this->select(
                'sender_params',
                [
                    'name',
                    'value',
                ],
                ['recipient_id' => $id['id']]
            );
            foreach($params as $param){
                $recipient[$i][$param['name']] = $param['value'];
            }
            ++$i;
        }

        return $recipient;
    }

    function total()
    {
        return $this->count(
            'sender_recipient',
            ["id"],
            ['campaign_id' => $this->campaignId]);
    }

    function filtered(){
        return $this->total();
    }
}