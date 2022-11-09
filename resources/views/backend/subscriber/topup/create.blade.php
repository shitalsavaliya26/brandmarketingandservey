@extends('layouts.backend.subscriber.main')
@section('title', 'Top Up')
@section('css')
<style>
   .credit-card-div {
      margin: auto;
      font-family: "Helvetica Neue", Helvetica, sans-serif;
      padding: 10px;
      margin-top: 20px;
      margin-bottom: 20px;
      border-radius: 15px;
      background: #f9fcff;
   }
   .save-card{
      padding-top: 35px;
   }
</style>
@endsection

@section('content')
<div class="row">
   <div class="col-md-12 grid-margin mb-0">
       <div class="row">
           <div class="col-12 col-xl-8 mb-4 mb-xl-0">
               <div class="d-flex flex-row bd-highlight mb-3">
                   <div class="p-1 bd-highlight"><a href="{{ route('subscriber.dashboard')}}" class="text-dark"><i class="mdi mdi-home mdi-36px" aria-hidden="true"></i></a></div>
                   <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                   <div class="p-1 bd-highlight mt-2"><a href="{{ route('topup.index')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Top Up History</h5></a></div>
                     <div class="p-1 bd-highlight mt-2"><a class="text-dark"> <i class=" mdi mdi-chevron-right  mdi-24px" aria-hidden="true"></i></a></div>
                   <div class="p-1 bd-highlight mt-2"><a href="{{ route('topup.create')}}" class="text-decoration-none text-dark"><h6 class="mt-1">Top Up</h5></a></div>
               </div>
           </div>
       </div>
   </div>
</div>
<div class="row">
   <div class="col-12 grid-margin">
      <div class="card">
         <div class="card-body">
            <h4 class="card-title">Top Up</h4>
            <!-- Example container -->

           
            {{-- new --}}
               <form role="form" action="{{ route('generateurl') }}" method="post" id="new-topup-form">
                  @csrf
                  <div class="row mb-3 mt-md-2">
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Amount</label>
                              <div class="input-group">
                                 <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white">{{ auth()->user()->currency->symbol ?? "" }}</span> 
                                 </div>
                                 <input type="text" class="form-control amount" placeholder="Enter amount" name="amount" id="amount"  oninput="restrict(this);"/>
                              </div>
                              <label id="amount-error" class="error" for="amount"></label>
                              <span class="help-block text-danger">{{ $errors->first('amount') }}</span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <button type="submit" class="btn btn-primary mr-2">Pay</button>
                  <a class="btn btn-light" href="{{route('topup.index')}}">Cancel</a>
               </form>
           

            {{-- old --}}
            {{-- <div class="align-items-center justify-content-between">
               <h4 class="card-title"></h4>
               <h4 class="card-title text-right">
                  <span class="btn btn-success btn-sm btn-xs-block new-card">Add New Card</span>
               </h4>
            </div>
            @if(isset($cards) && count($cards) > 0)
            <div id="saved-title">
               <div class="row credit-card-div">
                  <!-- Cards -->
                  @foreach($cards as $card)
                     <div class="col-md-3" >
                        <!-- Visa - selectable -->
                        <div class="credit-card @if($card->brand == 'Visa') visa @elseif($card->brand == 'MasterCard') mastercard @elseif($card->brand == 'American Express') amex  @elseif($card->brand == 'Discover') discover @elseif($card->brand == 'Diners Club') diners @elseif($card->brand == 'JCB') jcb @elseif($card->brand == 'UnionPay') unionpay @endif selectable" onclick= "fillForm('{{ $card->id }}')" >
                           <div class="credit-card-last4 selectable">
                              {{$card->last4}}
                           </div>
                           <div class="credit-card-expiry">
                              {{$card['expiration']}}
                           </div>
                        </div>
                     </div>
                  @endforeach
               </div>
            </div>
            <div id="saved-card-store">
                     <form role="form" action="{{ route('topup.store') }}" class="saved-topup-form" method="post" id="saved-topup-form" data-stripe-publishable-key="{{ config('services.stripe.stripe_key') }}">
                        @csrf
                        <input id="card_id" type="hidden" name="card_id">

                        <div class="row mb-3">
                           <div class="col-md-3">
                              <div class="form-group row">
                                 <div class="col-12">
                                    <label class="">CVV</label>
                                    <input type="text" class="form-control cvv" placeholder="CVV(ex. 123)" name="cvv" id="cvv" maxlength="3" minlength="3" />
                                    <span class="help-block text-danger">{{ $errors->first('cvv') }}</span>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group row">
                                 <div class="col-12">
                                    <label class="">Amount</label>
                                    <input type="text" class="form-control amount" placeholder="Enter amount" name="amount" id="amount"  oninput="restrict(this);"/>
                                    <span class="help-block text-danger">{{ $errors->first('amount') }}</span>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">Pay</button>
                        <a class="btn btn-light" href="{{route('topup.index')}}">Cancel</a>
                     </form>
                  </div>
            @endif
            <div id="topup-form-div">
               <form role="form" action="{{ route('topup.store') }}" class="topup-form" method="post" data-stripe-publishable-key="{{ config('services.stripe.stripe_key') }}" id="topup-form">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Name on Card</label>
                              <input type="text" class="form-control capital-input" placeholder="Name on Card" name="nameoncard" id="nameoncard">
                              <span class="help-block text-danger">{{ $errors->first('nameoncard') }}</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Card Number</label>
                              <input type="text" class="form-control cardnumber" name="cardnumber" id="cardnumber" data-inputmask-placeholder="*" data-inputmask="'mask': '9999 9999 9999 9999'" />
                              <span class="help-block text-danger">{{ $errors->first('cardnumber') }}</span>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row mb-3 mt-md-2">
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">CVV</label>
                              <input type="text" class="form-control cvv" placeholder="CVV(ex. 123)" name="cvv" id="cvv" maxlength="3" minlength="3" />
                              <span class="help-block text-danger">{{ $errors->first('cvv') }}</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Expiration (MM/YY)</label>
                              <input type="text" class="form-control" name="expirationdate" id="expirationdate" >
                              <span class="help-block text-danger">{{ $errors->first('expirationdate') }}</span>

                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row mb-3 mt-md-2">
                     <div class="col-md-6">
                        <div class="form-group row">
                           <div class="col-12">
                              <label class="">Amount</label>
                              <input type="text" class="form-control amount" placeholder="Enter amount" name="amount" id="amount"  oninput="restrict(this);"/>
                              <span class="help-block text-danger">{{ $errors->first('amount') }}</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 save-card">
                        <div class="form-group">
                           <div class="form-check">
                              <label class="form-check-label">
                                 <input type="checkbox" class="form-check-input" name="savecard" id="savecard" value="1">
                                 Save card for future use
                                 <i class="input-helper"></i>
                              </label>
                           </div>
                        </div>
                     </div>
                  </div>

                  <button type="submit" class="btn btn-primary mr-2">Pay</button>
                  <a class="btn btn-light" href="{{route('topup.index')}}">Cancel</a>
               </form>
            </div> --}}
         </div>
      </div>
   </div>
</div>
@endsection

@section('script')
<script type="text/javascript" src="{{asset('backend/js/custom/member.js') .'?v='.time() }}"></script>
<script type="text/javascript" src="{{asset('backend/vendors/inputmask/jquery.inputmask.bundle.js')}}"></script>
<script type="text/javascript" src="{{asset('backend/js/stripe/stripe.js')}}"></script>
<script type="text/javascript">
   $(".amount").keypress(function (e) {
      if(e.which == 46){
         if($(this).val().indexOf('.') != -1) {
               return false;
         }
      }

      if (e.which != 8 && e.which != 0 && e.which != 46 && (e.which < 48 || e.which > 57)) {
         return false;
      }
   });

   $("#cvv").keypress(function (e) {
      if (String.fromCharCode(e.keyCode).match(/[^0-9]/g)) return false;
   });

   function restrict(tis) {
      var prev = tis.getAttribute("data-prev");
      prev = (prev != '') ? prev : '';
      if (Math.round(tis.value*100)/100!=tis.value)
      tis.value=prev;
      tis.setAttribute("data-prev",tis.value)
   }

   $(".new-card").click(function(){
      $('#topup-form-div').show();
      $("#saved-card-store").hide();
      $('#saved-topup-form')[0].reset();
   });

   /*
   * Credit card expiry date formatting (real-time)
   */
   $(document).on('keyup blur', '#expirationdate', function(event) {
      var currentDate = new Date();
      var currentMonth = ("0" + (currentDate.getMonth() + 1)).slice(-2);
      var currentYear = String(currentDate.getFullYear()).slice(-2);

      var cardExpiryArray = $('#expirationdate').val().split('/');
      var userMonth = cardExpiryArray[0],
      userYear = cardExpiryArray[1];

      if (userYear < currentYear) {
         $(this).attr('aria-invalid', 'true');
         $('label[for=expirationdate]').remove();
         $('input[name="expirationdate"]').after('<label id="expirationdate-error" class="error" for="expirationdate">Year must be greater than current year</label>');
      } else if (userYear <= currentYear && userMonth < currentMonth) {
         $(this).attr('aria-invalid', 'true');
         $('label[for=expirationdate]').remove();
         $('input[name="expirationdate"]').after('<label id="expirationdate-error" class="error" for="expirationdate">Year and month must be greater than current year and month</label>');
      } else if (userYear > parseInt(currentYear)+5) {
         $(this).attr('aria-invalid', 'true');
         $('label[for=expirationdate]').remove();
         $('input[name="expirationdate"]').after('<label id="expirationdate-error" class="error" for="expirationdate">Year must be less than 5 from current year</label>');
      } else {
         $(this).attr('aria-invalid', 'false');
      }
   });

   $(":input").inputmask();

   $('#expirationdate').inputmask({alias: 'datetime', inputFormat: 'mm/yy', placeholder:'MM/YY'});

   $(function() {
      $('#topup-form-div').hide();
      $("#saved-card-store").hide();
      var $form = $(".topup-form");
      $('form.topup-form').bind('submit', function(e) {
         e.preventDefault();
         Stripe.setPublishableKey($form.data('stripe-publishable-key'));

         var word = $('#expirationdate').val();
         var split = word.split("/");
         var month = split[0];
         var year = split[1];
         Stripe.createToken({
               number: $('.cardnumber').val(),
               cvc: $('.cvv').val(),
               exp_month: month,
               exp_year: year
         }, stripeResponseHandler);
      });
      function stripeResponseHandler(status, response) {
         if (response.error) {
               $('.error')
                  .removeClass('hide')
                  .find('.alert')
                  .text(response.error.message);
         } else {
               var token = response['id'];
               $form.find('input[type=text]').empty();
               $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
               $form.get(0).submit();
         }
      }
   });

   function fillForm(card) {
      $("#saved-card-store").show();
      $('#topup-form-div').hide();
      $('input[name="card_id"]').val(card);
      $('#topup-form')[0].reset();
   }

</script>
@endsection
