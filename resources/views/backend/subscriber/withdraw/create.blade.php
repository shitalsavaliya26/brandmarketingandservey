@extends('layouts.backend.subscriber.main')
@section('title', 'Withdraw')

@section('content')
<div class="row">
   <div class="col-md-12 grid-margin mb-0">
       <div class="row">
           <div class="col-12 col-xl-8 mb-4 mb-xl-0">
               <div class="d-flex flex-row bd-highlight mb-3">
                   <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                   <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                   <div class="p-1 bd-highlight mt-2"><a href="{{ route('withdraw.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Withdraw History</h5></a></div>
                   <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                   <div class="p-1 bd-highlight mt-2"><a href="{{ route('withdraw.create')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Withdraw</h5></a></div>
               </div>
           </div>
       </div>
   </div>
</div>
<div class="row">
   <div class="col-12 grid-margin">
      <div class="card">
         <div class="card-body">
            <h4 class="card-title">Withdraw</h4>
            <!-- Example container -->
            <div id="withdraw-form-div">
               <form role="form" action="{{ route('withdraw.store') }}" class="withdraw-form" method="post" id="withdraw-form">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Bank Name</label>
                              <input type="text" class="form-control capital-input" placeholder="Enter bank name" name="bank_name" id="bank_name">
                              <span class="help-block text-danger">{{ $errors->first('bank_name') }}</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Branch Name</label>
                              <input type="text" class="form-control capital-input" placeholder="Branch name" name="branch_name" id="branch_name" />
                              <span class="help-block text-danger">{{ $errors->first('branch_name') }}</span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row mb-3 mt-md-2">
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Account Holder Name</label>
                              <input type="text" class="form-control capital-input" placeholder="Enter account holder name" name="acc_holder_name" id="acc_holder_name" />
                              <span class="help-block text-danger">{{ $errors->first('acc_holder_name') }}</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Account Number</label>
                              <input type="text" class="form-control" name="acc_number" id="acc_number" placeholder="Enter account number" >
                              <span class="help-block text-danger">{{ $errors->first('acc_number') }}</span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row mb-3 mt-md-2">
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Swift Code</label>
                              <input type="text" class="form-control" placeholder="Enter Swift code" name="ifsc" id="ifsc" />
                              <span class="help-block text-danger">{{ $errors->first('ifsc') }}</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Amount</label>
                              <input type="text" class="form-control amount" placeholder="Enter amount" name="amount" id="amount" />
                              <span class="help-block text-danger">{{ $errors->first('amount') }}</span>
                           </div>
                        </div>
                     </div>
                  </div>

                  <button type="submit" class="btn btn-primary mr-2">Withdraw</button>
                  <a class="btn btn-light" href="{{route('withdraw.index')}}">Cancel</a>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
@endsection
