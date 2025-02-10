<?php

namespace App\Services;

class AccountService
{
    private $account;

    public function __construct($account)
    {
        $this->account = $account;
    }

    public function account_balance()
    {
        $account = $this->account;
        $balance = 0;
        
        $opening_balance = $account->opening_balance;
        if($account->type == 'Credit'){
          $balance += $account->opening_balance;
        }else{
           $balance -= $account->opening_balance;   
        }

        // $account_adjustments = AccountAdjustment::where('account_id',$id)->get();
        // foreach ($account_adjustments as $item) {
        //     if($item->type == 'Credit'){
        //       $balance += $item->rate;
        //     }else{
        //        $balance -= $item->rate;   
        //     }
        // }
        
       
        // $sendTransfer = Transaction::where('sender_id',$id)->sum('amount');
        // $balance -= $sendTransfer;
        // $receiveTransfer = Transaction::where('receiver_id',$id)->sum('amount');
        // $balance += $receiveTransfer;
        
        // $payable = Expense::where('account_id',$id)->where('Type','Debit')->sum('amount');
        // $receivables = Expense::where('account_id',$id)->where('Type','Credit')->sum('amount');
        // $balance -= $payable;
        // $balance += $receivables;
        
        // $vendor = VendorPayable::where('account_id',$id)->sum('amount');
        // $balance -= $vendor;
        
        // $customer = CustomerReceivable::where('receive_in','Account')->where('receiver_id',$id)->sum('amount');
        // $balance += $customer;
        
        return $balance;
    }
    
}