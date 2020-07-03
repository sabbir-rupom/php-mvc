<?php

namespace App\Controllers;

use \Core\Controller as AppController;
use App\Models\TableData;
use Valitron\Validator;

/**
 * Home controller
 */
class Home extends AppController
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function index()
    {
        $this->renderView('form', ['menu' => 'hm']);
    }

    /**
     * Validate and process submitted form data
     */
    public function processForm()
    {
        $response = [
          'success' => false,
          'message' => ''
        ];

        if (getMethod() === 'POST') {
            $validator = new Validator(inputPost());
            $validator->rule('required', ['amount', 'receipt_id', 'buyer', 'buyer_email', 'item', 'city', 'phone', 'entry_by', 'timezone']);
            $validator->rule('email', 'buyer_email');
            $validator->rule('lengthMax', 'buyer', 20);
            $validator->rule('numeric', ['phone', 'entry_by', 'amount']);
            $validator->rule('array', 'item');

            if ($validator->validate()) {
                $resultId = $this->processDataInsertion();
                if ($resultId) {
                    $response['success'] = true;
                    $response['message'] = 'Data added successfully';
                } else {
                    $response['message'] = 'Unable to insert data';
                }
            } else {
                $response['message'] = textMessage($validator->errors());
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Process table data insertion
     *
     * @return mixed
     */
    protected function processDataInsertion()
    {
        $receiptId = trim(inputPost('receipt_id', FILTER_SANITIZE_STRING));
        $items = filter_var(inputPost('item'), FILTER_SANITIZE_STRING, FILTER_FORCE_ARRAY);

        $modelTable = new TableData();

        $timezone = trim(inputPost('timezone'));
        if (in_array($timezone, \DateTimeZone::listIdentifiers())) {
            date_default_timezone_set($timezone);
        } else {
            date_default_timezone_set('UTC');
        }

        $insertData = [
          'amount' => inputPost('amount', FILTER_SANITIZE_NUMBER_INT),
          'buyer' => trim(inputPost('buyer', FILTER_SANITIZE_STRING)),
          'receipt_id' => $receiptId,
          'items' => rtrim(implode(',', $items), ','),
          'buyer_email' => inputPost('buyer_email', FILTER_SANITIZE_EMAIL),
          'note' => strip_tags(inputPost('note', FILTER_SANITIZE_STRING)),
          'buyer_ip' => getIpAddress(),
          'city' => trim(inputPost('city', FILTER_SANITIZE_STRING)),
          'phone' => inputPost('phone', FILTER_SANITIZE_NUMBER_INT),
          'hash_key' => hash('sha512', $receiptId . $modelTable->saltKey),
          'entry_at' => date('Y-m-d H:i:s'),
          'entry_by' => inputPost('entry_by', FILTER_SANITIZE_NUMBER_INT),
        ];

        return $modelTable->insert($insertData);
    }

//    public function test() {
//        $tabledata = new TableData();
//        $data = [
//          'amount' => 200,
//          'buyer' => 'Okata',
//          'receipt_id' => 'JHSI23',
//          'items' => 'HGYS,HNys',
//          'buyer_email' => 'aaa@hgds.cojn',
//          'note' => 'zxczxczxczxczxczxczxzxc  dsfdsfsd sdfsdfdsf',
//          'buyer_ip' => getIpAddress(),
//          'city' => 'Dhaka',
//          'phone' => '880197364785',
//          'hash_key' => hash('sha512', '11223344' . $tabledata->saltKey),
//          'entry_at' => date('Y-m-d'),
//          'entry_by' => 123
//        ];
//
//        $result = $tabledata->insert($data);
//
//        echo '<pre>';
//
//        $dd = $tabledata->find($result);
//
//        print_r($dd);
//
//        echo $tabledata->update([
//          'buyer_email' => 'aaa@pangkha.mal'
//            ], ['id' => $result]);
//
//        $dd = $tabledata->findBy(['id' => $result], 'id, amount, buyer_email', true);
//
//        print_r($dd);
//
//
//        $dd = $tabledata->findAllBy(
//            ['id' => 1, 'amount' => 200, 'buyer_email like' => 'aaa'],
//            'id, amount, phone, city',
//            ['id' => 'DESC'],
//            ['limit' => 10, 'offset' => 0]
//        );
//
//        print_r($dd);
//
//        print_r($tabledata->countBy());
//    }
}
