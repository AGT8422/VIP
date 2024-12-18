@lang('purchase.supplier'):
<address>
    @php
        $id = $transaction->contact->id;
        $account = \App\Account::where("contact_id",$id)->first();
    @endphp
    <strong>{{ $transaction->contact->supplier_business_name }}</strong>
    <button class="btn btn-link">
        <a href="account/account/{{$account->id}}" target="_blank">
            <strong>{{ $transaction->contact->name }}</strong>
        </a>
    </button> 
    {!! $transaction->contact->contact_address !!}
    @if(!empty($transaction->contact->tax_number))
        <br>@lang('contact.tax_no'): {{$transaction->contact->tax_number}}
    @endif
    @if(!empty($transaction->contact->mobile))
        <br>@lang('contact.mobile'): {{$transaction->contact->mobile}}
    @endif
    @if(!empty($transaction->contact->email))
        <br>Email: {{$transaction->contact->email}}
    @endif
</address>