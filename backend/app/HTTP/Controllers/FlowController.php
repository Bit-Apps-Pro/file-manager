<?php

namespace BitApps\FM\HTTP\Controllers;

use BitApps\FM\Core\Database\FlowModel;
use BitApps\FM\Core\Http\Router\Router;
use BitApps\FM\Core\Util\IpTool;
use BitApps\FM\Model\Flow;
use BitApps\FM\Log\LogHandler as Log;

final class FlowController
{
    private static $_integrationModel;

    /**
     * Constructor of FlowController
     *
     * @return void
     */
    public function __construct()
    {
        static::$_integrationModel = new Flow();
    }

    /**
     * Retrieved flows from DB based on conditions
     *
     * @param  array  $conditions Conditions to retrieve flows
     * @param  array  $columns    Columns to select
     * @return array|WP_Error
     */
    public function get($conditions = [], $columns = [])
    {
        if (empty($columns)) {
            $columns = [
                'id',
                'name',
                'triggered_entity',
                'triggered_entity_id',
                'flow_details',
                'status',
                'user_id',
                'user_ip',
                'created_at',
                'updated_at',

            ];
        }

        return static::$_integrationModel->get(
            $columns,
            $conditions
        );
    }

    /**
     * Save Flows to DB
     *
     * @param  string  $name                Name of the flow
     * @param  string  $triggered_entity    Triggered form name
     * @param  int  $triggered_entity_id ID of the triggered form
     * @param  object  $flow_details        Path of the flow it will go through after triggered
     * @param  bool  $status              Status of the flow. Disabled or Enabled.
     * @return int|WP_Error
     */
    public function save($name, $triggered_entity, $triggered_entity_id, $flow_details, $status = null)
    {
        if ($status == null) {
            $status = 1;
        }
        $user_details = IpTool::getUserDetail();

        return static::$_integrationModel->insert(
            [
                'name' => $name,
                'triggered_entity' => $triggered_entity,
                'triggered_entity_id' => $triggered_entity_id,
                'flow_details' => is_string($flow_details) ? $flow_details : wp_json_encode($flow_details),
                'status' => $status,
                'user_id' => $user_details['id'],
                'user_ip' => $user_details['ip'],
                'created_at' => $user_details['time'],
                'updated_at' => $user_details['time'],
            ]
        );
    }

    /**
     * Update Flows to DB
     *
     * @param  int  $id   ID of the flow to update
     * @param  array  $data Data to update
     * @return int|WP_Error
     */
    public function update(
        $id,
        $data
    ) {
        $user_details = IpTool::getUserDetail();
        $columnToUpdate = [
            'user_id' => $user_details['id'],
            'user_ip' => $user_details['ip'],
            'updated_at' => $user_details['time'],
        ];
        if (isset($data['name'])) {
            $columnToUpdate['name'] = $data['name'];
        }
        if (isset($data['triggered_entity'])) {
            $columnToUpdate['triggered_entity'] = $data['triggered_entity'];
        }
        if (isset($data['triggered_entity_id'])) {
            $columnToUpdate['triggered_entity_id'] = $data['triggered_entity_id'];
        }
        if (isset($data['flow_details'])) {
            $columnToUpdate['flow_details'] = $data['flow_details'];
        }

        return static::$_integrationModel->update(
            $columnToUpdate,
            ['id' => $id]
        );
    }

    /**
     * Updates Flow status to DB
     *
     * @param  int  $id     ID of the flow to update
     * @param  bool  $status Status of the flow. Disabled or Enabled.
     * @return int|WP_Error
     */
    public function updateStatus($id, $status)
    {
        return static::$_integrationModel->update(
            [
                'status' => $status,
                'user_id' => $this->_user_details['id'],
                'user_ip' => $this->_user_details['ip'],
                'updated_at' => $this->_user_details['time'],
            ],
            [
                'id' => $id,
            ]
        );
    }

    /**
     * Deletes Flow from DB
     *
     * @param  int  $flowID ID of the flow to delete.
     * @return bool|WP_Error
     */
    public function delete($flowID)
    {
        $delStatus = static::$_integrationModel->delete(
            [
                'id' => $flowID,
            ]
        );
        if (is_wp_error($delStatus)) {
            return $delStatus;
        }
        Log::delete((object) ['flow_id' => $flowID]);

        return $delStatus;
    }

    public function bulkDelete($flowID)
    {
        $delStatus = static::$_integrationModel->bulkDelete(
            [
                'id' => $flowID,
            ]
        );
        if (is_wp_error($delStatus)) {
            return $delStatus;
        }
        Log::delete((object) ['flow_id' => $flowID]);

        return $delStatus;
    }

    public function handle()
    {
        $router = Router::instance();
        var_dump($router->getBasePrefix());
           if (is_user_logged_in()) {
            return "OK";
           } else {
            return "NO";
           }
    }
}
