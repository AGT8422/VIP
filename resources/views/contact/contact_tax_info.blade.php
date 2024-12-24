<strong><i class="fa fa-info margin-r-5"></i> @lang('contact.tax_no')</strong>
<p class="text-muted font_number">
    {{ $contact->tax_number }}
</p>
@if($contact->pay_term_type)
    <strong><i class="fa fa-calendar margin-r-5"></i> @lang('contact.pay_term_period')</strong>
    <p class="text-muted font_number">
        {{ __('lang_v1.' . $contact->pay_term_type) }}
    </p>
@endif
@if($contact->pay_term_number)
    <strong><i class="fas fa fa-handshake margin-r-5"></i> @lang('contact.pay_term')</strong>
    <p class="text-muted font_number">
        {{ $contact->pay_term_number }}
    </p>
@endif