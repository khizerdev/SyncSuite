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
                            <h3 class="card-title">Vendor Payables Update</h3>
                        </div>
                        <form method="post" action="{{ route('accounts-vendors-payables.update', $module->id) }}">
                            @csrf

                            <div class="card">
                                <div class="card-body">
                                    <div class="container-fluid">
                                        <div class="row">

                                            <div class="form-group col-md-3">
                                                <label for="date">Date</label>
                                                <input type="date" name="date" class="form-control date"
                                                    value="{{ \Carbon\Carbon::parse($module->date)->format('M-d-Y') }}" />
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Select Vendor</label>
                                                    <select name="vendor_id" class="vendor_id form-control">
                                                        @foreach (\App\Models\Vendor::all() as $vendor)
                                                            <option data-id="{{ Con::vendor_balance($vendor->id) }}"
                                                                @if ($module->vendor_id == $vendor->id) {{ 'selected' }} @endif
                                                                value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Balance</label>
                                                    <input readonly value="0" class="vendor_balance form-control" />
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Select Account</label>
                                                    <select name="account_id" class="form-control ">
                                                        @foreach (\App\Models\Account::all() as $account)
                                                            <option
                                                                @if ($module->account_id == $account->id) {{ 'selected' }} @endif
                                                                value="{{ $account->id }}">{{ $account->account_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Payment Type</label>
                                                    <select name="payment_type" class="form-control payment_type">
                                                        <option
                                                            @if ($module->payment_type == 'Cash') {{ 'selected' }} @endif>
                                                            Cash</option>
                                                        <option
                                                            @if ($module->payment_type == 'Cheque') {{ 'selected' }} @endif>
                                                            Cheque</option>
                                                        <option
                                                            @if ($module->payment_type == 'Online Transfer') {{ 'selected' }} @endif>
                                                            Online Transfer</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 bank_name">
                                                <div class="form-group">
                                                    <label>Bank Name</label>
                                                    <input value="{{ $module->bank_name }}" name="bank_name"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-3 branch_name">
                                                <div class="form-group">
                                                    <label>Branch Name</label>
                                                    <input value="{{ $module->branch_name }}" name="branch_name"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-3 account_title">
                                                <div class="form-group">
                                                    <label>Account Title</label>
                                                    <input value="{{ $module->account_title }}" name="account_title"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-3 cheque">
                                                <div class="form-group">
                                                    <label>Cheque #</label>
                                                    <input value="{{ $module->cheque }}" name="cheque"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-3 cheque_date">
                                                <div class="form-group">
                                                    <label>Cheque Date</label>
                                                    <input value="{{ $module->cheque_date->format('M-d-Y') }}"
                                                        name="cheque_date" type="date" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Amount</label>
                                                    <input min="0" step=".01" value="{{ $module->amount }}"
                                                        required name="amount" type="number" class="amount form-control">
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
                                                    <textarea name="remarks" class="form-control">{{ $module->remarks }}</textarea>
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
    </section>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {

            $('.vendor_id,.amount').on('change', function() {
                call();
            }).change();


            function call() {

                let balance = $('.vendor_id').find(':selected').data('id');
                let amount = $('.amount').val();
                balance = parseFloat(balance);
                let result = balance - amount;
                $('.vendor_balance').val(balance);
                $('.new_balance').val(result);

            }


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
