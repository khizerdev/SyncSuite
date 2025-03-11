@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h1 class="m-0">Branch</h1> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        {{-- <li class="breadcrumb-item"><a class="btn btn-secondary" href="{{ url('/branches') }}">View List</a>
                        </li> --}}
                        {{-- <li class="breadcrumb-item active">Create</li> --}}
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Customer Receivables</h3>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('accounts-customersreceivables.store') }}">
                                @csrf

                                <div class="card">
                                    <div class="card-body">
                                        <div class="container-fluid">
                                            <div class="row">

                                                <div class="form-group col-md-3">
                                                    <label for="date">Date</label>
                                                    <input type="date" name="date" class="form-control date" />
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Select Customer</label>
                                                        <select name="customer_id" class="customer_id form-control ">
                                                            <option selected value="" disabled>Select Customer
                                                            </option>
                                                            @foreach (\App\Models\Customer::all() as $customer)
                                                                <option data-id="{{ $customer->id }}"
                                                                    value="{{ $customer->id }}">{{ $customer->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group ">
                                                        <label>Balance</label>
                                                        <input readonly value="0" class="old_balance form-control">
                                                        <div class="spinner" style="display: none;">
                                                            <div class="spinner-border spinner-border-sm text-primary"
                                                                role="status">
                                                                <span class="visually-hidden"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Receive in:</label>
                                                        <select name="receive_in" class="form-control receive_in">
                                                            <option>Account</option>
                                                            <option>Vendor</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 vendor">
                                                    <div class="form-group">
                                                        <label>Select Vendor</label>
                                                        <select name="vendor_id" class="form-control">
                                                            @foreach (\App\Models\Vendor::all() as $vendor)
                                                                <option value="{{ $vendor->id }}">{{ $vendor->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 account">
                                                    <div class="form-group">
                                                        <label>Select Account</label>
                                                        <select name="account_id" class="form-control">
                                                            @foreach (\App\Models\Account::all() as $account)
                                                                <option value="{{ $account->id }}">
                                                                    {{ $account->account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Payment Type</label>
                                                        <select name="payment_type" class="form-control payment_type">
                                                            <option>Cash</option>
                                                            <option>Cheque</option>
                                                            <option>Online Transfer</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 bank_name">
                                                    <div class="form-group">
                                                        <label>Bank Name</label>
                                                        <input name="bank_name" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 branch_name">
                                                    <div class="form-group">
                                                        <label>Branch Name</label>
                                                        <input name="branch_name" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 account_title">
                                                    <div class="form-group">
                                                        <label>Account Title</label>
                                                        <input name="account_title" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 cheque">
                                                    <div class="form-group">
                                                        <label>Cheque #</label>
                                                        <input name="cheque" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 cheque_date">
                                                    <div class="form-group">
                                                        <label>Cheque Date</label>
                                                        <input name="cheque_date" type="date" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-3 ">
                                                    <div class="form-group">
                                                        <label>Amount</label>
                                                        <input step=".01" min="0" required name="amount"
                                                            type="number" class="amount form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group ">
                                                        <label>New Balance</label>
                                                        <input readonly class="new_balance form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-12 ">
                                                    <div class="form-group ">
                                                        <label>Remarks</label>
                                                        <textarea name="remarks" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 pt-5 py-3 text-center">
                                                    <button type="submit" class="btn btn-info">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.customer_id').on('change', function() {
                var customerId = $(this).val();
                const spinner = document.querySelector('.spinner');
                const balanceDiv = document.querySelector('.old_balance');

                if (customerId) {
                    spinner.style.display = 'block';
                    balanceDiv.value = '';
                    balanceDiv.disabled = true;
                    $.ajax({
                        url: "{{ route('customer-balance') }}",
                        data: {
                            customer_id: customerId
                        },
                        type: 'GET',
                        success: function(response) {
                            console.log(response)
                            spinner.style.display = 'none';
                            balanceDiv.disabled = false;
                            call(response.balance)
                        },
                        error: function(response) {
                            console.log(response)
                            spinner.style.display = 'none';
                            balanceDiv.disabled = false;
                            balanceDiv.value = '';
                        }
                    });
                }

            });

            function call(remain) {

                let balance = remain
                let amount = $('.amount').val();

                balance = parseFloat(balance);

                let result = balance - amount;


                $('.customer_balance_balance').val(balance);

                $('.old_balance').val(balance);

                $('.new_balance').val(result);
            }
        });
    </script>

    <script>
        $(document).ready(function() {




            $('.customer_id,.amount').on('change', function() {
                call();
            }).change();


            function call() {

                let balance = $('.old_balance').val();
                let amount = $('.amount').val();

                balance = parseFloat(balance);

                let result = balance - amount;


                $('.customer_balance_balance').val(balance);

                //  $('.old_balance').val(balance);

                $('.new_balance').val(result);
            }


            $('.receive_in').on('change', function() {

                if (this.value == 'Vendor') {
                    $('.account').hide();
                    $('.vendor').show();
                } else {
                    $('.account').show();
                    $('.vendor').hide();
                }

            }).change();


            $('.payment_type').on('change', function() {

                if (this.value == 'Cash') {
                    $('.bank_name').hide();
                    $('.branch_name').hide();
                    $('.account_title').hide();
                    $('.cheque').hide();
                    $('.cheque_date').hide();

                } else if (this.value == 'Cheque') {

                    $('.bank_name').show();
                    $('.branch_name').show();
                    $('.account_title').show();
                    $('.cheque').show();
                    $('.cheque_date').show();

                } else {

                    $('.bank_name').show();
                    $('.branch_name').show();
                    $('.account_title').show();
                    $('.cheque').hide();
                    $('.cheque_date').hide();

                }

            }).change();


        });
    </script>
@endsection
