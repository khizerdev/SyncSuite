@extends('layouts.app')
@section('content')
<div class="container-fluid">
   <x-content-header title="Create Product" />
   <div  data-v-app="">
      <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="typeModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <form class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="typeModalLabel">Create Product Type</h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label for="name">Name</label><input type="text" class="form-control" id="name" required=""><!---->
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <label for="Material">Material</label>
                        <select class="form-control" id="material" required="">
                           <option disabled="" value="">Select Material</option>
                           <option value="1">Rubber</option>
                           <option value="2">Steel</option>
                           <option value="3">Plastic</option>
                           <option value="4">rubber2</option>
                           <option value="5">Bearing</option>
                           <option value="6">Timing belts</option>
                           <option value="7">Front Yarn</option>
                        </select>
                        <!---->
                     </div>
                     <div class="col-md-6">
                        <label for="particular">Particular</label>
                        <select class="form-control" id="particular" required="">
                           <option disabled="" value="">Select Particular</option>
                        </select>
                        <!---->
                     </div>
                  </div>
               </div>
               <div class="modal-footer"><button type="button" class="btn btn-secondary">Close</button><button type="submit" class="btn btn-primary">Create</button></div>
            </form>
         </div>
      </div>
      <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="materialModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <form class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="materialModalLabel">Create Material</h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label for="name">Name</label><input type="text" class="form-control" id="name"><!---->
                  </div>
                  <div class="form-group">
                     <label for="particular">Particular</label>
                     <select class="form-control" id="particular">
                        <option disabled="" value="">Select Particular</option>
                        <option value="1">Particulars 1</option>
                        <option value="3">Particulars 2</option>
                        <option value="4">Part 3</option>
                        <option value="5">Test 5</option>
                        <option value="6">Test 5</option>
                        <option value="7">particular 1</option>
                        <option value="8">particular 1</option>
                        <option value="9">particular 1</option>
                        <option value="10">test</option>
                        <option value="11">abc</option>
                        <option value="12">Machine Parts</option>
                        <option value="13">Machine Parts</option>
                        <option value="14">Raw Material</option>
                        <option value="15">Raw Material</option>
                        <option value="16">Raw Material</option>
                     </select>
                     <!---->
                  </div>
               </div>
               <div class="modal-footer"><button type="button" class="btn btn-secondary">Close</button><button type="submit" class="btn btn-primary">Create</button></div>
            </form>
         </div>
      </div>
      <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="partModalLabel" aria-hidden="true">
         <div class="modal-dialog" role="document">
            <form class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="partModalLabel">Create Particular</h5>
                  <button part="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label for="name">Name</label><input part="text" class="form-control" id="name"><!---->
                  </div>
               </div>
               <div class="modal-footer"><button part="button" class="btn btn-secondary">Close</button><button part="submit" class="btn btn-primary">Create</button></div>
            </form>
         </div>
      </div>
      <div class="container-fluid">
         <div class="row">
            <div class="col-md-12">
               <div class="card card-secondary">
                  <div class="card-header">
                     <h3 class="card-title">Create</h3>
                  </div>
                  <div class="card-body">
                     <form>
                        <div class="row mb-2">
                           <div class="col-md-6">
                              <label for="name">Product Name</label><input type="text" class="form-control" id="name" required=""><!---->
                           </div>
                           <div class="col-md-6">
                              <label for="department">Department</label>
                              <select class="form-control" id="department" required="">
                                 <option disabled="" value="">Select Department</option>
                                 @php
                                 $departments = \App\Models\ErpDepartment::all();
                                 @endphp
                                 @foreach($departments as $department)
                                    <option value="{{$department->id}}">{{$department->title}}</option>
                                 @endforeach
                                 
                              </select>
                              <!---->
                           </div>
                        </div>
                        <div class="row mb-2">
                           <div class="col-md-4">
                              <label for="material">Type</label>
                              <select class="form-control" id="material" required="">
                                 <option disabled="" value="">Select Type</option>
                              </select>
                              <!---->
                              <p class="mt-2 text-sm cursor-pointer"> Create Type </p>
                           </div>
                           <div class="col-md-4">
                              <label for="particular">Category</label>
                              <select class="form-control" id="particular" required="">
                                 <option disabled="" value="">Select Category</option>
                              </select>
                              <!---->
                              <p class="mt-2 text-sm cursor-pointer"> Create Category </p>
                           </div>
                        </div>
                        <div class="row mb-4">
                           <div class="col-md-3">
                              <label for="openingQuantity">Opening Quantity</label><input type="number" class="form-control" id="openingQuantity" required=""><!---->
                           </div>
                           <div class="col-md-3">
                              <label for="openingInventory">Opening Inventory Price</label><input type="number" class="form-control" id="openingInventory" required=""><!---->
                           </div>
                           <div class="col-md-3"><label for="totalPrice">Total Price</label><input type="number" class="form-control" id="totalPrice" required="" readonly=""></div>
                           <div class="col-md-3">
                              <label for="min_qty_limit">Min Quantity Limit</label><input type="text" class="form-control" id="min_qty_limit" required=""><!---->
                           </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection