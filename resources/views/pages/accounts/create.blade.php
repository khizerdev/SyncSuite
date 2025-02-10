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
                            <h3 class="card-title">Account Create</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('accounts.store') }}" method="POST" data-method="POST"
                                data-method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="date">Date</label>
                                            <input type="date" name="date" class="form-control date" />
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Account Type</label>
                                            <select class="form-control account_type" name="account_type">
                                                <option value="Cash">Cash Account</option>
                                                <option value="Bank">Bank Account</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="simpleinput">Account Name</label>
                                            <input required name="account_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="bank col-sm-3">
                                        <div class="form-group">
                                            <label for="simpleinput">Account Title</label>
                                            <input name="account_title" class="form-control">
                                        </div>
                                    </div>
                                    <div class="bank col-sm-3">
                                        <div class="form-group">
                                            <label for="simpleinput">Person Name</label>
                                            <input name="person_name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="bank col-sm-3">
                                        <div class="bank form-group">
                                            <label>Account Number</label>
                                            <input name="account_number" class="form-control">
                                        </div>
                                    </div>
                                    <div class="bank col-sm-3">
                                        <div class="form-group">
                                            <label for="simpleinput">Branch Code</label>
                                            <input name="branch_code" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Opening Balance</label>
                                            <select class="form-control opening_balance">
                                                <option>No</option>
                                                <option>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="bal col-sm-3">
                                        <div class="form-group">
                                            <label>Balance Type</label>
                                            <select class="form-control" name="balance_type">
                                                <option>Credit</option>
                                                <option>Debit</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="bal col-sm-3">
                                        <div class="form-group">
                                            <label>Amount</label>
                                            <input min="0" type="number" name="opening_balance"
                                                class="form-control amount" value="0">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-secondary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {

            $('.account_type').on('change', function() {
                if (this.value == 'Cash') {
                    console.log("reaching")
                    $('.bank input').val('');
                    $('.bank').hide();
                } else {
                    $('.bank').show();
                }

            }).change();


            $('.opening_balance').on('change', function() {

                if (this.value == 'Yes') {
                    $('.bal').show();
                } else {
                    $('.bal').hide();
                    $('.bal input').val(0);
                }

            }).change();


        });
    </script>
@endsection
