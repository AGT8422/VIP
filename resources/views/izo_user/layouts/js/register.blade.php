@section('javascript')
    <script type="text/javascript">
            

            

            
            $(document).ready(function() {
                $(document).on('click','.language_box',function(){
                    $(".list_of_lang").toggleClass('hide');
                });
                let isValid = true;
                let debounceTimer;
                const debounceDelay = 500; // Adjust the delay as needed (milliseconds)
                
                function debounce(func, delay) {
                    return function() {
                        const context = this;
                        const args    = arguments;
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => func.apply(context, args), delay);
                    };
                }
                
                var company_name      = false;
                var emails            = false;
                var mobile            = false;
                var domain_name       = false;
                var passwords         = false;
                var confirm_passwords = false;
                
                function sendCode(){
                    $html  = '<div class="activation_code_content">';
                    $html += '<div class="form_active">';
                    $html += '<h1>{!!__("izo.ACTIVATION CODE")!!}</h1>';
                    $html += '<br>';
                    $html += '<h3>{!!__("izo.enter_code")!!}</h3>';
                    $html += '<p style="font-size:17px;color: #3a3a3a88 !important ;">{!!__("izo.please_check_email")!!}</p>';
                    $html += '<input type="number" id="activation_code_number" oninput="limitInput(this)" maxlength="6" name="activation_code_number" class="izo-form-input code_activate_input" placeholder="XXXXXX"><br>';
                    // $html += '<span class="btn btn-primary" style="width:100px;font-size: 20px;" onclick="sendCode();">{!!__("izo.Activate")!!}</span>';
                    $html += '<br>';
                    $html += '<span onclick="resendCode();" class="link_code">{!!__("izo.resend_code")!!}</span>';
                    $html += '</div>';
                    $html += '</div>'; 
                    $('.activation_code').css({'display':'block'});
                    $('.activation_code').html($html);
                    $("#activation_code_number").on('input',debounce(function(){
                        var parent = $(this) ;
                        var global = $(this).parent().parent().parent() ;
                        var form   = $('#first_register_form');
                        $.ajax({
                            url:"/email-code-check",
                            dataType: 'json',
                            data:{
                                email:$("#email").val(),
                                code:$(this).val(),
                            },
                            success:function(result){ 
                                if(result.success == 1){
                                    parent.css({"border":"2px solid Green","color":"Green"})
                                    global.css({"display":"none"});
                                    form.submit()
                                     
                                }else{
                                    parent.css({"border":"2px solid red","color":"red"}); 
                                }
                            
                            },
                        });	 
                    },debounceDelay));
                    function resendCode(){
                        sendCode();
                    }
                }

                $("#company_name").on('input',debounce(function(){
                    
                    
                    var spin    = $(this).parent().find('.spinner');
                    var correct = $(this).parent().find('.success-icon');
                    var wrong   = $(this).parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                    wrong.parent().find('.error').html('');

                    value = $(this).val();
                    $.ajax({
                        "type":"GET",
                        "url":"{{route('checkCompanyName')}}",
                        "dataType":"json",
                        "data":{company_name:$(this).val()},
                        "success":function(data){ 


                            if(data.message === false && value != ""){
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                wrong.parent().find('.error').html("{!!__('izo.already_exist')!!}");
                                wrong.addClass('hide_icon');
                                company_name = true;
                            }else{
                                if(value != ""){
                                    // console.log( "length of ### : "+ correct.parent().find('.izo-form-input').val().length );
                                    if(correct.parent().find('.izo-form-input').val().length < 3){
                                        spin.addClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        wrong.removeClass('hide_icon');
                                        wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                        wrong.parent().find('.error').html("{!!__('izo.desc_company')!!}");
                                        wrong.addClass('hide_icon');
                                        company_name = false;
                                    }else{
                                        spin.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        correct.removeClass('hide_icon');
                                        correct.parent().find('.izo-form-input').css({"border":"2px solid green","color":"green"});
                                        wrong.parent().find('.error').html('');
                                        company_name = true;
                                    }
                                }else{
                                    spin.addClass('hide_icon');
                                    wrong.addClass('hide_icon');
                                    correct.addClass('hide_icon');
                                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                                    company_name = false;
                                } 
                            } 
                            console.log('company_name '  +  company_name);



                        },
                        "error":function(xhr,status,error){
                            
                        }
                    })
                },debounceDelay));

                $("#email").on('input',debounce(function(){
                    
                    
                    var spin    = $(this).parent().find('.spinner');
                    var correct = $(this).parent().find('.success-icon');
                    var wrong   = $(this).parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                    wrong.parent().find('.error').html('');

                    value = $(this).val();
                    $.ajax({
                        "type":"GET",
                        "url":"{{route('checkEmail')}}",
                        "dataType":"json",
                        "data":{email:$(this).val()},
                        "success":function(data){ 



                            if(data.message === false && value != ""){
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                wrong.parent().find('.error').html("{!!__('izo.already_exist')!!}");
                                emails = false;
                                wrong.addClass('hide_icon');
                            }else{
                                if(value != ""){
                                        if(!validateEmail(value)){
                                            spin.addClass('hide_icon');
                                            correct.addClass('hide_icon');
                                            wrong.removeClass('hide_icon');
                                            wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                            emails = false;
                                            wrong.parent().find('.error').html("{!!__('izo.desc_email')!!}");
                                            wrong.addClass('hide_icon');
                                        }else{
                                            spin.addClass('hide_icon');
                                            wrong.addClass('hide_icon');
                                            correct.removeClass('hide_icon');
                                            correct.parent().find('.izo-form-input').css({"border":"2px solid green","color":"green"});
                                            wrong.parent().find('.error').html('');
                                            emails = true;
                                        }
                                    }else{
                                        spin.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                                        wrong.parent().find('.error').html('');
                                        emails = false;
                                    } 
                            } 



                            // console.log('emails '  +  emails);

                        },
                        "error":function(xhr,status,error){
                            
                        }
                    })
                },debounceDelay));

                $("#domain_name").on('input',debounce(function(){
                    
                    var spin    = $(this).parent().parent().parent().find('.spinner');
                    var correct = $(this).parent().parent().parent().find('.success-icon');
                    var wrong   = $(this).parent().parent().parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                    wrong.parent().find('.error').html('');
                    value = $(this).val();
                    $.ajax({
                        "type":"GET",
                        "url":"{{route('checkDomainName')}}",
                        "dataType":"json",
                        "data":{domain_name:$(this).val()},
                        "success":function(data){ 
                            if(data.message === false && value != ""){
                                $('#domainError').html("{!!__('izo.desc_domain')!!}");
                                $('#domainError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                domain_name = true;
                            }else{
                                if(value != ""){
                                    if(correct.parent().find('.izo-form-input').val().length < 3){
                                        spin.addClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        wrong.removeClass('hide_icon');
                                        wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                        wrong.parent().find('.error').html("{!!__('izo.desc_company')!!}");
                                        wrong.addClass('hide_icon');
                                        company_name = false;
                                    }else{
                                        spin.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        correct.removeClass('hide_icon');
                                        correct.parent().find('.izo-form-input').css({"border":"2px solid green","color":"green"});
                                        domain_name = true;
                                    }
                                }else{
                                    spin.addClass('hide_icon');
                                    wrong.addClass('hide_icon');
                                    correct.addClass('hide_icon');
                                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                                    domain_name = false;
                                } 
                            } 
                            // console.log('domain_name '  +  domain_name);
                        },
                        "error":function(xhr,status,error){
                            
                        }
                    })
                },debounceDelay));

                $("#mobile").on('input',debounce(function(){
                    
                    var spin    = $(this).parent().parent().find('.spinner');
                    var correct = $(this).parent().parent().find('.success-icon');
                    var wrong   = $(this).parent().parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                    max   = $(this).attr('data-max');
                    value = $(this).val();
                    $.ajax({
                        "type":"GET",
                        "url":"{{route('checkMobile')}}",
                        "dataType":"json",
                        "data":{mobile:$(this).val(),mobile_code:$('#mobile_code').val()},
                        "success":function(data){ 
                            if(data.message === false && value != ""){
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                mobile = false;
                            }else{
                                if(value != ""){
                                    // console.log(value.length);
                                    // console.log(parseFloat(max));
                                    if(value.length > parseFloat(max)){
                                        // spin.addClass('hide_icon');
                                        // correct.addClass('hide_icon');
                                        // wrong.removeClass('hide_icon');
                                        wrong.parent().find('.izo-form-input').css({"border":"2px solid red","color":"red"});
                                        // mobile = false;
                                        spin.addClass('hide_icon');
                                        wrong.removeClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        // wrong.parent().find('.izo-form-input-mobile').css({"border": "2px solid red","color":"red"}); 
                                        $('#mobileError').html("{!!__('izo.desc_number')!!}"+parseFloat(max)+" {!!__('izo.desc_number_num')!!}");
                                        $('#mobileError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                                        spin.addClass('hide_icon');
                                        correct.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        mobile = false;
                                    }else{
                                        spin.addClass('hide_icon');
                                        wrong.addClass('hide_icon');
                                        correct.removeClass('hide_icon');
                                        correct.parent().find('.izo-form-input').css({"border":"2px solid green","color":"green"});
                                        $('#mobileError').text(''); 
                                        mobile = true;
                                    }
                                }else{
                                    spin.addClass('hide_icon');
                                    wrong.addClass('hide_icon');
                                    correct.addClass('hide_icon');
                                    wrong.parent().find('.izo-form-input').css({"border": "1px solid #e86000;","color":"#2c2c2c"});
                                    mobile = true;
                                } 
                            } 
                            // console.log('mobile '  +  mobile);
                        },
                        "error":function(xhr,status,error){
                            
                        }
                    })
                },debounceDelay));

                $("#password").on('input',debounce(function(){
                    
                    var spin    = $(this).parent().find('.spinner');
                    var correct = $(this).parent().find('.success-icon');
                    var wrong   = $(this).parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input-password').css({"border": "1px solid #e86000","color":"#2c2c2c"});
                    wrong.parent().find('.error').html('');
                    
                    value = $(this).val();
                    if(value.length >  6){
                        if(!/[a-z]/.test(value)){
                            spin.addClass('hide_icon');
                            wrong.removeClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                            $('#passwordError').html();
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            passwords = false;
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                        }else if(!/[A-Z]/.test(value)){
                            spin.addClass('hide_icon');
                            wrong.removeClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                            $('#passwordError').html("{!!__('izo.desc_password')!!}");
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            passwords = false;
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                        }else if(!/[0-9]/.test(value)){
                            spin.addClass('hide_icon');
                            wrong.removeClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                            $('#passwordError').html("{!!__('izo.desc_password')!!}");
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            passwords = false;
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                        }else{
                            if(value != $("#confirm-password").val()){
                                spin.addClass('hide_icon');
                                wrong.removeClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                                $('#passwordError').html("");
                                $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                                $("#confirm-password").css({"border": "2px solid red","color":"red"});  
                                $('#passwordConfirmError').html("{!!__('izo.desc_not_match')!!}");
                                $('#passwordConfirmError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                                passwords = false;
                                spin.addClass('hide_icon');
                                correct.addClass('hide_icon');
                                wrong.addClass('hide_icon');
                            }else{
                                spin.addClass('hide_icon');
                                wrong.addClass('hide_icon');
                                correct.removeClass('hide_icon');
                                correct.parent().find('.izo-form-input-password').css({"border":"2px solid green","color":"green"});
                                $("#confirm-password").css({"border": "2px solid green","color":"green"});  
                                $('#passwordConfirmError').text('');
                                $('#passwordConfirmError').parent().find('.izo-form-input').css({"border": "2px solid green;","color":"green"});
                                wrong.parent().find('.error').html('');
                                passwords = true;
                            }
                        }
                    } else{
                        spin.addClass('hide_icon');
                        wrong.removeClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"});
                        $('#passwordError').html("{!!__('izo.desc_password')!!}");
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        passwords = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                          
                    } 
                    // console.log('passwords '  +  passwords);
                },debounceDelay));
                
                $("#confirm-password").on('input',debounce(function(){
                    
                    var spin    = $(this).parent().find('.spinner');
                    var correct = $(this).parent().find('.success-icon');
                    var wrong   = $(this).parent().find('.fa-times-circle');
                    
                    ($(this).val() == '')?spin.addClass('hide_icon'):spin.removeClass('hide_icon');
                    correct.addClass('hide_icon');
                    wrong.addClass('hide_icon');
                    wrong.parent().find('.izo-form-input-password').css({"border": "1px solid #e86000","color":"#2c2c2c"});
                    
                    value = $(this).val();
                    if(value.length > 6){
                        if(value !=  $("#password").val()){
                            spin.addClass('hide_icon');
                            wrong.removeClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                            $('#passwordConfirmError').html("{!!__('izo.desc_not_match')!!}");
                            $('#passwordConfirmError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            $("#password").css({"border": "2px solid red","color":"red"});  
                            $('#passwordError').html("{!!__('izo.desc_not_match')!!}");
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                            passwords = false;
                            confirm_passwords = false;
                        }else{
                            spin.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                            correct.removeClass('hide_icon');
                            correct.parent().find('.izo-form-input-password').css({"border":"2px solid green","color":"green"});
                            $('#passwordConfirmError').text('');
                            $("#password").css({"border": "2px solid green","color":"green"}); 
                            $('#passwordError').text('');
                            $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid green;","color":"green"});
                            passwords = true;
                            confirm_passwords = true;
                            spin.addClass('hide_icon');
                            correct.addClass('hide_icon');
                            wrong.addClass('hide_icon');
                        }
                    }else{
                        spin.addClass('hide_icon');
                        wrong.removeClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.parent().find('.izo-form-input-password').css({"border": "2px solid red","color":"red"}); 
                        $('#passwordConfirmError').html("{!!__('izo.desc_not_match')!!}");
                        $('#passwordConfirmError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                        
                        // spin.addClass('hide_icon');
                        // wrong.removeClass('hide_icon');
                        // correct.addClass('hide_icon');
                        // wrong.find('.izo-form-input-password').css({"border": "1px solid red","color":"red"});
                        passwords = false;
                        confirm_passwords = false;
                    } 
                    // console.log('passwords '  +  password);
                    // console.log('confirm_passwords '  +  confirm_passwords);
                },debounceDelay));
                
                
                
                 
                
                $('#first_register_form').on('submit', function(e) {
                    // Prevent default form submission
                    e.preventDefault();
                    // console.log(
                    //     company_name,
                    //     emails,
                    //     mobile,
                    //     domain_name,
                    //     passwords,
                    //     confirm_passwords,
                    // );
                    if(company_name == true && domain_name  == true && emails == true && passwords == true && mobile == true && confirm_passwords == true ){
                        isValid = true;
                        $('.error').text('');
                    }else{
                        isValid = false;
                    }

                    // Clear previous errors
                    // Validate form fields
                    const name     = $('#company_name').val().trim();
                    const email    = $('#email').val().trim();
                    const password = $('#password').val().trim();
                    
                    if (name === '') {
                        $('#nameError').html("{!!__('izo.desc_company_require')!!}");
                        $('#nameError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        var spin    = $('#nameError').parent().find('.spinner');
                        var correct = $('#nameError').parent().find('.success-icon');
                        var wrong   = $('#nameError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    }
                    
                    if (email === '') {
                        $('#emailError').html("{!!__('izo.desc_email_require')!!}");
                        $('#emailError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        var spin    = $('#emailError').parent().find('.spinner');
                        var correct = $('#emailError').parent().find('.success-icon');
                        var wrong   = $('#emailError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if (!validateEmail(email)) {
                        $('#emailError').html("{!!__('izo.desc_email')!!}");
                        $('#emailError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        var spin    = $('#emailError').parent().find('.spinner');
                        var correct = $('#emailError').parent().find('.success-icon');
                        var wrong   = $('#emailError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    }
                     
                    if (password === '') {
                        $('#passwordError').html("{!!__('izo.desc_password_require')!!}");
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if (password.length <  6) {
                        $('#passwordError').html("{!!__('izo.desc_password')!!}");
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if(!/[a-z]/.test(password)){
                        $('#passwordError').html("{!!__('izo.desc_password')!!}");
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if(!/[A-Z]/.test(password)){
                        $('#passwordError').html("{!!__('izo.desc_password')!!}");
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    } else if(!/[0-9]/.test(password)){
                        $('#passwordError').html("{!!__('izo.desc_password')!!}");
                        $('#passwordError').parent().find('.izo-form-input').css({"border": "2px solid red;","color":"red"});
                        var spin    = $('#passwordError').parent().find('.spinner');
                        var correct = $('#passwordError').parent().find('.success-icon');
                        var wrong   = $('#passwordError').parent().find('.fa-times-circle');
                        isValid = false;
                        spin.addClass('hide_icon');
                        correct.addClass('hide_icon');
                        wrong.addClass('hide_icon');
                    }
                    
                    console.log('isValid '  +  isValid);
                    if (isValid) {
                        // If the form is valid, submit it
                        $(".loading").css({"display":"block"}); 
                        if($("#otp").val()==0){
                            $("#otp").attr("value",1);
                            sendCode();
                            $.ajax({
                                url:"/email-code-activation",
                                dataType: 'json',
                                data:{
                                    email:$("#email").val(),
                                },
                                success:function(result){  
                                    if(result.success == 1){
                                    }   
                                },
                            });	    
                        }else{
                            this.submit();
                        }
                    }
                });
                
                function resendCode(){
                    sendCode();
                }
               

                function validateEmail(email) {
                    const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                    return re.test(email);
                }
                var checked     = 0;
                var list_domain = JSON.parse($("#domain_name_array").val());  
                for( i in list_domain){ 
                    if(list_domain[i] == window.location.hostname){ 
                        checked = 1;
                    } 
                }  
                console.log( "hostname : " + window.location.hostname);
                if( window.location.hostname != "izocloud.com" ){
                    if(checked == 0 ){ 
                        $("form#go-home").submit();
                    }   
                }   
            });


            function limitInput(element) {
                if (element.value.length > 6) {
                    element.value = element.value.slice(0, 6);
                }
            }
            document.addEventListener('DOMContentLoaded', function () {
                const togglePassword = document.querySelector('#togglePassword');
                const password = document.querySelector('#password');
                const confirm_password = document.querySelector('#confirm-password');

                togglePassword.addEventListener('click', function () {
                    // Toggle the type attribute using getAttribute() and setAttribute()
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    const confirm_type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    confirm_password.setAttribute('type', confirm_type);

                    // Toggle the eye icon
                    this.classList.toggle('eye-icon--active');
                });
                
                const selectSelected = document.querySelector('.select-selected');
                const selectItems = document.querySelector('.select-items');
                const mobile_code = $('#mobile_code');
                const options = selectItems.querySelectorAll('div');
               
                selectSelected.addEventListener('click', function () {
                    selectItems.classList.toggle('select-arrow-active');
                    selectItems.style.display = selectItems.style.display === 'block' ? 'none' : 'block';
                });

                options.forEach(option => {
                    option.addEventListener('click', function () {
                        selectSelected.innerHTML = this.innerHTML;
                        selectSelected.dataset.value = this.dataset.value;
                        mobile_code.attr('value',this.dataset.value);
                        selectItems.style.display = 'none';
                        options.forEach(opt => opt.classList.remove('same-as-selected'));
                        this.classList.add('same-as-selected');
                    });
                });

                document.addEventListener('click', function (e) {
                    if (!selectSelected.contains(e.target) && !selectItems.contains(e.target)) {
                        selectItems.style.display = 'none';
                    }
                });
            });
    </script>
@endsection