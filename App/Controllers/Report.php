<?php

namespace App\Controllers;

use \Core\Controller as AppController;
use App\Models\TableData;

/**
 * Report controller
 */
class Report extends AppController
{

    /**
     * Show reporting page
     *
     * @return void
     */
    public function index()
    {
        $modelData = new TableData();

        $orderArray = $filterArray = [];

        $orderBy = trim(inputGet('order_by', FILTER_SANITIZE_URL));
        if (in_array($orderBy, ['entry_at', 'entry_by'])) {
            $direction = strtolower(inputGet('direction', FILTER_SANITIZE_URL)) === 'desc' ? 'desc' : 'asc';
            $orderArray = [$orderBy => $direction];
        } else {
            $direction = '';
        }

        if (inputPost('filter')) {
            $filterArray = $this->prepareFilterCondition();
        }

        $reportData = $modelData->findAllBy($filterArray, 'amount,buyer,receipt_id,items,buyer_email,note,city,phone,entry_at,entry_by', $orderArray, [], true);

        $this->renderView('report', [
          'menu' => 'rp',
          'reportData' => $reportData,
          'order' => $orderBy,
          'direction' => $direction
        ]);
    }

    /**
     * Prepare filter parameters from Form POST
     *
     * @return array Filter Array
     */
    protected function prepareFilterCondition(): array
    {
        $filterArr = [];

        $startDate = validDate(inputPost('from_date'));
        if ($startDate) {
            $filterArr['entry_at >='] = $startDate;
            setFlashData('from_date', $startDate);
        }

        $endDate = validDate(inputPost('to_date'));
        if ($endDate) {
            $filterArr['entry_at <='] = $endDate;
            setFlashData('to_date', $endDate);
        }

        $entryBy = inputPost('entry_by', FILTER_SANITIZE_NUMBER_INT);
        if (!empty($entryBy)) {
            $filterArr['entry_by'] = $entryBy;
            setFlashData('entry_by', $entryBy);
        }

        return $filterArr;
    }
}
