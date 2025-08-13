<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('purchase-invoice.store') }}" method="post">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        <label>Date</label>
                        <input type="date" name="date" required class="form-control" />     
                        @if($errors->has('date'))
                            <div class="error text-danger">{{ $errors->first('date') }}</div>
                        @endif
                    </div>
                    <div class="col-6">
                        <label>Due Date</label>
                        <input type="date" name="due_date" required class="form-control" />     
                    </div>
                    <div class="col-md-4 mt-3">
                        <label>Cartage</label>
                        <input type="number" name="cartge" class="cartage form-control" required/>     
                    </div>
                    <div class="col-md-5 mt-3">
                                            <div class="form-group">
                                                <label for="simpleinput">Description</label>
                                                <input type="text" name="descr" class="form-control" />
                                            </div>
                                        </div>
                </div>
            </div>
            
            <div class="invoice card mt-4">
                <div class="card-body">
                    <div class="container-fluid">
                        @csrf
                        <input type="hidden" name="receipt_id" value="{{ $receipts->first()->id }}" />
                        
                        <div class="pt-4 invoice-header">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Rate</th>
                                        <th style="width:115px;">GST</th>
                                        <th>Total</th>
                                    </tr>        
                                </thead>
                                <tbody class="invoice_items">
                                    @foreach($receipts as $receipt)
                                        @foreach($receipt->items as $receiptItem)
                                            <?php $key = uniqid(); ?>
                                            <tr>
                                                <td class="text-center"><input type="hidden" name="items[{{$key}}][id]" value="{{$receiptItem->id}}" /> {{$receiptItem->id}}</td>
                                                <td>{{ $receiptItem->product ? $receiptItem->product->name : 'N/A' }}</td>
                                                <td class="text-center weight">{{$receiptItem->qty}}</td>
                                                <td class="text-center rate">{{$receiptItem->rate}}</td>
                                                <td class="text-center"><input required min="0" name="items[{{$key}}][gst]" type="number" value="0" class="form-control gst" /></td>
                                                <td class="text-center total">0</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" style="border: none;"></td>
                                        <td class="text-center">Subtotal:</td>
                                        <td class="text-center subtotal"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="border: none;"></td>
                                        <td class="text-center">GST:</td>
                                        <td class="text-center tgst"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="border: none;"></td>
                                        <td class="text-center">Grand Total:</td>
                                        <td class="text-center gtotal"></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="text-center mt-3">
                                <input type="submit" class="btn btn-primary" value="Create Invoice" />    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>        
    $(document).ready(function() {
        $('input').change(function(){
            let subtotal = 0;
            let tgst = 0;
            let gtotal = 0;
            let cartage = $('.cartage').val() || 0;

            $('.invoice_items').children().each(function(){     
                let qty = parseFloat($(this).find('.weight').text());
                let pp = parseFloat($(this).find('.rate').text());
                let gst = parseFloat($(this).find('.gst').val()) || 0;
                let linetotal = qty * pp;
                
                subtotal += linetotal;
                
                let gstcal = linetotal * (gst / 100);
                linetotal = linetotal + gstcal;
                
                tgst += gstcal;
                
                $(this).find('.total').text(linetotal.toFixed(2));
            });
                
            gtotal = subtotal + tgst + parseFloat(cartage);
                
            $('.subtotal').text(subtotal.toFixed(2));
            $('.tgst').text(tgst.toFixed(2));
            $('.gtotal').text(gtotal.toFixed(2));
        });
        
        $("input").change();
    });
</script>