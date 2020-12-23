<?php
namespace App\Traits;

trait ApiResponse{
    /**
     * @var array
     */
    protected $response = [];
    protected $status = 200;

    /**
     * @Description set response
     * @param array $data
     * @param $message
     * @param int $apiStatus
     * @param int $status
     * @Author Khuram Qadeer.
     */
    public function setResponse($message, $apiStatus = 1, $status = 200, $data = [])
    {
        $this->status = $status;
        $this->response['status'] = $apiStatus;
        $this->response['message'] = $message;
        $this->response['data'] = $data;
    }
}
