<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerReceivable;

class CustomerReceivableService
{

    public function customer_balance($id){
       
        $balance = 0; 
        $customer = Customer::find($id);
     
        
        if($customer->balance_type == 'Credit'){
            $balance += $customer->opening_balance;
        }else{
            $balance -= $customer->opening_balance ;
        }

        // $customer_adjustments = CustomerAdjustment::where('customer_id',$id)->get();
        // foreach ($customer_adjustments as $item) {
        //     if($item->type == 'Credit'){
        //       $balance += $item->rate;
        //     }else{
        //        $balance -= $item->rate;   
        //     }
        // }
        
        $customerR = CustomerReceivable::where('customer_id',$id)->sum('amount');
        $balance -= $customerR;
    
        // $saleBills = SaleBill::all();
        // foreach($saleBills as $key => $saleBill){
           
        //     $SalebillSingle = $this->get_sale_bill($saleBill->id);
        //     if($SalebillSingle['customer_id'] == $id){
        //         $balance += $SalebillSingle['total'];
        //     }
        // }
        
        return $balance;
    }
    
}