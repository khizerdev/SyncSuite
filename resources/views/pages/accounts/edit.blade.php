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
                            <form method="post" action="{{ route('accounts.update', $module->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="container-fluid">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="form-group col-md-3">
                                                <label for="date">Date</label>
                                                <input value="{{ $module->date }}" type="date" name="date"
                                                    class="form-control date">
                                            </div>

                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label>Account Type</label>
                                                    <select class="form-control account_type" name="account_type">
                                                        <option @if ($module->account_type == 'Cash') {{ 'selected' }} @endif
                                                            value="Cash">Cash Account</option>
                                                        <option @if ($module->account_type == 'Bank') {{ 'selected' }} @endif
                                                            value="Bank">Bank Account</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="simpleinput">Account Name</label>
                                                    <input required value="{{ $module->account_name }}" name="account_name"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div @if ($module->account_type == 'Cash') style="display:none;" @endif
                                                class="bank col-md-3">
                                                <div class="form-group">
                                                    <label for="simpleinput">Account Title</label>
                                                    <input value="{{ $module->account_title }}" name="account_title"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div @if ($module->account_type == 'Cash') style="display:none;" @endif
                                                class="bank col-md-3">
                                                <div class="form-group">
                                                    <label for="simpleinput">Person Name</label>
                                                    <input value="{{ $module->person_name }}" name="person_name"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div @if ($module->account_type == 'Cash') style="display:none;" @endif
                                                class="bank col-md-3">
                                                <div class=" form-group">
                                                    <label>Account Number</label>
                                                    <input value="{{ $module->account_number }}" name="account_number"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div @if ($module->account_type == 'Cash') style="display:none;" @endif
                                                class="bank col-md-3">
                                                <div class="form-group">
                                                    <label for="simpleinput">Branch Code</label>
                                                    <input value="{{ $module->branch_code }}" name="branch_code"
                                                        type="text" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Opening Balance</label>
                                                    <select class="form-control opening_balance" name="opening_balance">
                                                        <option>No</option>
                                                        <option
                                                            @if ($module->opening_balance > 0) {{ 'selected' }} @endif>
                                                            Yes</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div @if ($module->opening_balance > 0) @else style="display:none;" @endif
                                                class="bal col-3">
                                                <div class="form-group">
                                                    <label>Balance Type</label>
                                                    <select class="form-control" name="balance_type">
                                                        <option
                                                            @if ($module->balance_type == 'Credit') {{ 'selected' }} @endif>
                                                            Credit</option>
                                                        <option
                                                            @if ($module->balance_type == 'Debit') {{ 'selected' }} @endif>
                                                            Debit</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div @if ($module->opening_balance > 0) @else style="display:none;" @endif
                                                class="bal form-group col-md-3">
                                                <label for="balance">Amount</label>
                                                <input type="number" id="amount" name="opening_balance"
                                                    class="form-control amount" value="{{ $module->opening_balance }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 pt-5 text-center">
                                            <button type="submit" class="btn btn-info">Submit</button>
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

            $('.account_type').on('change', function() {

                if (this.value == 'Cash') {

                    $('.bank input').val('');
                    $('.bank').hide();
                } else {
                    $('.bank').show();
                }

            });


            $('.opening_balance').on('change', function() {

                if (this.value == 'Yes') {
                    $('.bal').show();
                } else {
                    $('.bal').hide();
                    $('.bal input').val(0);
                }

            });

        });
    </script>
@endsection
