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
        return $this->database->select(
            'senderCampaigns',
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
            $result = $this->database->update( 'senderCampaigns',
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
            $result = $this->database->insert( 'senderCampaigns',
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

        $data =  $this->database->select(
            'senderRecipient',
            ["id"],
            ['campaignId' => $this->campaignId, 'LIMIT' => [$start, $lenght]]);
        $recipient = [];
        foreach($data as $id){
            $params = $this->database->select(
                'senderParams',
                [
                    'name',
                    'value',
                ],
                ['recipientId' => $id['id']]
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
        if( is_array( $this->database->select( 'senderCampaigns', '*', ['id' => $this->campaignId] ) ) ){
            foreach($data as $recipient){
                $id = $this->database->insert('senderRecipient',['campaignId'=>$this->campaignId]);
                foreach($recipient as $name => $value){
                    $this->database->insert( 'senderParams',
                        [
                            'recipientId' => $id,
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
        $data = $this->database->query(
                'SELECT DISTINCT sp.name
                  FROM senderParams sp
                  INNER JOIN senderRecipient sr
                  ON sr.id=sp.recipientId
                  AND sr.campaignId='.$this->database->quote((int)$this->campaignId)
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
        $data =  $this->database->select(
            'senderRecipient',
            ["id"],
            ['campaignId' => $this->campaignId, 'LIMIT' => [$start, $lenght]]);
        $recipient = [];
        $i = 0;
        foreach($data as $id){
            $params = $this->database->select(
                'senderParams',
                [
                    'name',
                    'value',
                ],
                ['recipientId' => $id['id']]
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
        return $this->database->count(
            'senderRecipient',
            ["id"],
            ['campaignId' => $this->campaignId]);
    }

    function filtered(){
        return $this->total();
    }
}