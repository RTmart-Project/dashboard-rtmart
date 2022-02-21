<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TxLogService
{

    public function insertTxLog($transactionID, $transactionType, $transactionMerchant, $jsonSend, $jsonReceive, $status)
    {
        $sql = DB::table('tx_transaction_log')
            ->insert([
                'TransactionID' => $transactionID,
                'TransactionType' => $transactionType,
                'TransactionMerchant' => $transactionMerchant,
                'JSONSend' => $jsonSend,
                'JSONReceive' => $jsonReceive,
                'SendOn' => date('Y-m-d H:i:s'),
                'ReceiveOn' => date('Y-m-d H:i:s'),
                'Status' => $status
            ]);
        
        return $sql;
    }

}