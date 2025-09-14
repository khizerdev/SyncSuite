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
                            <h3 class="card-title">Create Sale Order</h3>
                        </div>
                        <div class="card-body">

                            <form action="{{ route('sale-orders.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="customer_id">Customer</label>
                                            <select name="customer_id" id="customer_id" class="form-control" required>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="order_status">Order Status</label>
                                            <select name="order_status" id="order_status" class="form-control" required>
                                                <option value="open">Open</option>
                                                <option value="hold">Hold</option>
                                                <option value="cleared">Cleared</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="order_reference">Order Reference</label>
                                            <input type="text" name="order_reference" id="order_reference"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="advance_payment">Advance Payment</label>
                                            <input type="number" step="0.01" name="advance_payment" id="advance_payment"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="delivery_date">Delivery Date</label>
                                            <input type="date" name="delivery_date" id="delivery_date"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payment_terms">Payment Terms</label>
                                            <textarea name="payment_terms" id="payment_terms" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section card mb-3" style="zoom:0.9">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Design Name</label>
                                                <select class="form-control design-name" name="design_name[]" required>
                                                    <option value="">Select Design</option>
                                                    @foreach (\App\Models\FabricMeasurement::all() as $design)
                                                        <option value="{{ $design->id }}"
                                                            data-stitch="{{ $design->design_stitch }}">
                                                            {{ $design->design_code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Colour</label>
                                                <select class="form-control" name="colour_id[]" required>
                                                    <option value="">Select Color</option>
                                                    @foreach (\App\Models\ColorCode::all() as $color)
                                                        <option value="{{ $color->id }}">
                                                            {{ $color->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Qty</label>
                                                <input type="number" class="form-control qty" name="qty[]" min="0"
                                                    step="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Lace Qty</label>
                                                <input type="number" class="form-control lace-qty" name="lace_qty[]"
                                                    min="0" step="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Rate</label>
                                                <input type="number" class="form-control rate" name="rate[]" readonly
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Stitch</label>
                                                <input type="text" class="form-control stitch" name="stitch[]"
                                                    readonly required>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Stitch Rate</label>
                                                <input type="number" class="form-control stitch-rate"
                                                    name="stitch_rate[]" value={{ \App\Models\Setting::find(2)->value }}>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Calc Stitch</label>
                                                <input type="text" class="form-control calculate_stitch"
                                                    name="calculate_stitch[]" readonly required>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>L/F</label>
                                                <input type="number" class="form-control length-factor"
                                                    name="length_factor[]" value={{ \App\Models\Setting::find(1)->value }}>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label>Amount</label>
                                                <input type="text" class="form-control amount" name="amount[]"
                                                    readonly required>
                                            </div>
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <div class="form-group w-100">
                                                <button type="button" class="btn btn-success btn-block add-section"><i
                                                        class="fas fa-plus"></i></button>
                                                <button type="button"
                                                    class="btn btn-danger btn-block remove-section mt-1"><i
                                                        class="fas fa-minus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
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
            
            var defaultStitchRate = {{ \App\Models\Setting::find(2)->value }};
            var defaultLengthFactor = {{ \App\Models\Setting::find(1)->value }};
            
            $(document).on('change', '.design-name', function() {
                var section = $(this).closest('.form-section');
                var selectedOption = $(this).find('option:selected');
                var stitchValue = selectedOption.data('stitch') || '';

                var calculatedStitch = (stitchValue / 1000).toFixed(4);
                section.find('.stitch').val(stitchValue);
                section.find('.rate').val(
                    (stitchValue / 1000) * {{ \App\Models\Setting::find(2)->value }} *
                    {{ \App\Models\Setting::find(1)->value }}
                    .toFixed(2));

                calculateSectionAmount(section);
            });

            // Add new section
            $("#addNewSection").click(function() {
                addNewSection();
            });

            // Add section button inside form
            // Modify your add-section click handler
            $(document).on('click', '.add-section', function() {
                var section = $(this).closest('.form-section');
                if (section.find('.design-name').val() === '') {
                    alert('Please select a Design Name before adding new row');
                    return;
                }
                addNewSection(section);
            });

            // Remove section
            $(document).on('click', '.remove-section', function() {
                if ($('.form-section').length > 1) {
                    $(this).closest('.form-section').remove();
                    calculateAllAmounts();
                } else {
                    alert("You need to have at least one section.");
                }
            });



            // Auto calculate amount when relevant fields change
            $(document).on('input', '.stitch-rate, .length-factor, .lace-qty', function() {
                var section = $(this).closest('.form-section');
                calculateSectionAmount(section);
            });

            // Modify the addNewSection function to validate before adding
            function addNewSection(afterSection) {
                // Check if current section has design selected
                if (afterSection && afterSection.find('.design-name').val() === '') {
                    alert('Please select a Design Name before adding new row');
                    return false;
                }
        
                var newSection = $('.form-section:first').clone();
                
                // Clear all inputs except the ones that need default values
                newSection.find('input').val('');
                newSection.find('select').val('');
                newSection.find('.stitch, .calculate_stitch, .amount, .rate').val('');
                
                // Set default values for stitch_rate and length_factor
                newSection.find('.stitch-rate').val(defaultStitchRate);
                newSection.find('.length-factor').val(defaultLengthFactor);
        
                if (afterSection) {
                    afterSection.after(newSection);
                } else {
                    $('#formSections').append(newSection);
                }
                return true;
            }

            // Add this validation check before processing form
            function validateForm() {
                var designNames = [];
                var isValid = true;

                $('.design-name').each(function() {
                    var val = $(this).val();
                    if (val === '') {
                        alert('Please select Design Name in all rows');
                        isValid = false;
                        return false; // breaks the each loop
                    }
                    // if (designNames.includes(val)) {
                    //     alert('Duplicate Design Name found: ' + $(this).find('option:selected').text());
                    //     isValid = false;
                    //     return false;
                    // }
                    designNames.push(val);
                });

                return isValid;
            }

            // Use this when submitting the form
            $('#bladeForm').submit(function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                }
            });

            // Add this for real-time duplicate checking
            $(document).on('change', '.design-name', function() {
                var currentVal = $(this).val();
                if (currentVal === '') return;

                // $('.design-name').not(this).each(function() {
                //     if ($(this).val() === currentVal) {
                //         alert('This design is already selected in another row');
                //         $(this).val('').trigger('change');
                //         return false;
                //     }
                // });
            });

            function calculateSectionAmount(section) {
                var rate = parseFloat(section.find('.rate').val()) || 0;
                var laceQty = parseFloat(section.find('.lace-qty').val()) || 0;

                // Amount = rate * lace qty
                var totalAmount = rate * laceQty;
                section.find('.amount').val(totalAmount.toFixed(2));

                // Keep stitch calculation display if needed
                var stitchValue = parseFloat(section.find('.stitch').val()) || 0;
                section.find('.calculate_stitch').val(stitchValue / 1000);
            }

            function calculateAllAmounts() {
                $('.form-section').each(function() {
                    calculateSectionAmount($(this));
                });
            }

        });
    </script>
@endsection
