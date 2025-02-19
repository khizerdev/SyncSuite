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
                        <form method="post" action="{{ route('accounts-transfers.store') }}">
                            @csrf

                            <div class="card">
                                <div class="card-body">
                                    <div class="container-fluid">
                                        <div class="py-3 container-fluid">
                                            <div class="row">

                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <label>Transform From (Account)</label>
                                                        <select class="form-control trasnfer_from" name="sender_id">
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->id }}">
                                                                    {{ $account->account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-3">
                                                    <div class="form-group">
                                                        <label>Transform To (Account)</label>
                                                        <select class="form-control trasnfer_to" name="receiver_id">
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->id }}">
                                                                    {{ $account->account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label for="date">Date</label>
                                                    <input required type="date" name="date" class="form-control date">
                                                </div>

                                                <div class="bal form-group col-md-3">
                                                    <label>Amount</label>
                                                    <input required min="1" type="number" name="amount"
                                                        class="form-control amount" value="0">
                                                    @if ($errors->has('amount'))
                                                        <div class="text-danger">{{ $errors->first('amount') }}</div>
                                                    @endif
                                                </div>

                                                <div class="bank col-md-12">
                                                    <div class="form-group">
                                                        <label for="simpleinput">Remarks</label>
                                                        <textarea name="remarks" class="form-control"></textarea>
                                                    </div>
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

            $('.account_type').on('change', function() {

                if (this.value == 'Cash') {
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
